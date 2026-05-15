<?php

namespace App\Services;

use App\Models\PlanPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Resolves the "active country" for a visitor.
 *
 * The session stores two keys instead of one so traveling visitors
 * don't get stuck on stale prices:
 *
 *   - country_source = 'explicit' → user clicked the country switcher
 *     or visited /ae, /sa, etc. directly. We persist this and DON'T
 *     re-detect, because the user made a deliberate choice.
 *
 *   - country_source = 'detected' (or unset) → we auto-detected from
 *     Cloudflare / ip-api on a previous request. On every subsequent
 *     request we re-run detection: if the visitor's IP now resolves
 *     to a different country (e.g. they traveled from Egypt to the
 *     UAE), the session is updated and they see local prices.
 *
 * Fallback chain when nothing's cached:
 *   1. Cloudflare's CF-IPCountry header (free, zero-latency)
 *   2. ip-api.com lookup (cached 24h per IP)
 *   3. 'EG' as the home-market default
 *
 * The returned code is always a supported country (one we have at
 * least one active PlanPrice row for) so the pricing page never
 * renders empty.
 */
class CountryDetector
{
    protected const SESSION_KEY = 'active_country';
    protected const SOURCE_KEY = 'active_country_source';
    protected const CACHE_TTL = 60 * 60 * 24; // 24 hours per-IP cache

    public function resolve(Request $request): string
    {
        $session = $request->session();
        $stored = $session->get(self::SESSION_KEY);
        $source = $session->get(self::SOURCE_KEY);

        // Explicit choice — user clicked the switcher or landed on
        // /ae, /sa, etc. Respect it without re-detection.
        if ($stored && $source === 'explicit') {
            return $this->ensureSupported(strtoupper($stored));
        }

        // Auto-detected previously, or never set. Re-detect every request
        // so travelers and shared-network users get the right currency.
        $detected = $this->fromCloudflare($request)
            ?? $this->fromIpLookup($request)
            ?? $stored
            ?? 'EG';

        $detected = strtoupper($detected);

        // Persist the freshly-detected code with the 'detected' source
        // so future requests can compare and update. Skip the write if
        // nothing changed — saves a session touch on every request.
        if ($stored !== $detected || $source !== 'detected') {
            $session->put(self::SESSION_KEY, $detected);
            $session->put(self::SOURCE_KEY, 'detected');
        }

        return $this->ensureSupported($detected);
    }

    /**
     * Persist an explicit country choice — locks the session to this
     * country until the user opts out (or clears their cookies).
     */
    public function setCountry(Request $request, string $code): void
    {
        $request->session()->put(self::SESSION_KEY, strtoupper($code));
        $request->session()->put(self::SOURCE_KEY, 'explicit');
    }

    /**
     * Drop the explicit lock so the next request re-detects from IP.
     * Useful if we ever want a "reset location" button.
     */
    public function clearExplicit(Request $request): void
    {
        $request->session()->forget(self::SOURCE_KEY);
    }

    /** List of country codes we have at least one active PlanPrice for. */
    public function supportedCountries(): array
    {
        return Cache::remember('plan_prices.supported_countries', 300, function () {
            return PlanPrice::where('is_active', true)
                ->select('country_code', 'country_name_ar', 'country_name_en', 'country_flag', 'currency_code')
                ->distinct('country_code')
                ->get()
                ->unique('country_code')
                ->values()
                ->toArray();
        });
    }

    protected function fromCloudflare(Request $request): ?string
    {
        $cf = $request->header('CF-IPCountry');
        // XX = unknown, T1 = Tor exit, EU = aggregated continent — all unusable
        // for pricing decisions. Fall through to ip-api.com lookup instead.
        return ($cf && strlen($cf) === 2 && !in_array($cf, ['XX', 'T1', 'EU'], true))
            ? $cf
            : null;
    }

    protected function fromIpLookup(Request $request): ?string
    {
        $ip = $request->ip();
        if (!$ip || str_starts_with($ip, '127.') || str_starts_with($ip, '192.168.') || $ip === '::1') {
            return null;
        }

        return Cache::remember("geoip:{$ip}", self::CACHE_TTL, function () use ($ip) {
            try {
                $res = Http::timeout(2)->get("http://ip-api.com/json/{$ip}", ['fields' => 'status,countryCode']);
                if ($res->ok() && $res->json('status') === 'success') {
                    return $res->json('countryCode');
                }
            } catch (\Throwable $e) {
                // Silent — we'll fall through to the default.
            }
            return null;
        });
    }

    /** If a detected country has no price rows yet, fall back to EG. */
    protected function ensureSupported(string $code): string
    {
        $supported = collect($this->supportedCountries())->pluck('country_code')->toArray();
        return in_array($code, $supported, true) ? $code : 'EG';
    }
}

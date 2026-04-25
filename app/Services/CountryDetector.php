<?php

namespace App\Services;

use App\Models\PlanPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Resolves the "active country" for a visitor in this order:
 *   1. Explicit user choice persisted in session.
 *   2. Cloudflare header (CF-IPCountry) — free and zero latency.
 *   3. ipapi.co / ip-api.com fallback lookup (cached by IP).
 *   4. 'EG' as the hard fallback (our home market).
 *
 * Return value is always a 2-letter ISO country code that has at least
 * one active PlanPrice row — otherwise we fall back to EG so the pricing
 * page never renders empty.
 */
class CountryDetector
{
    protected const SESSION_KEY = 'active_country';
    protected const CACHE_TTL = 60 * 60 * 24; // 24 hours per-IP cache

    public function resolve(Request $request): string
    {
        $code = $this->fromSession($request)
            ?? $this->fromCloudflare($request)
            ?? $this->fromIpLookup($request)
            ?? 'EG';

        return $this->ensureSupported(strtoupper($code));
    }

    /** Persist the user's choice for future requests. */
    public function setCountry(Request $request, string $code): void
    {
        $request->session()->put(self::SESSION_KEY, strtoupper($code));
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

    protected function fromSession(Request $request): ?string
    {
        return $request->session()->get(self::SESSION_KEY);
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

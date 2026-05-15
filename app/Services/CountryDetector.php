<?php

namespace App\Services;

use App\Models\PlanPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        // Auto-detect from the current request. Cloudflare first
        // (zero-latency); ip-api second (cached 24h per IP).
        // We INTENTIONALLY don't fall back to $stored here — if the
        // visitor was previously detected as EG and now their IP says
        // nothing, we'd rather show 'EG' (the home market default)
        // than the stale session value, which is what kept travelers
        // pinned to the old currency.
        $detected = $this->fromCloudflare($request)
            ?? $this->fromIpLookup($request)
            ?? 'EG';

        $detected = strtoupper($detected);

        // Persist the freshly-detected code with the 'detected' source
        // so future requests can compare. Skip the write when nothing
        // changed to avoid touching the session on every request.
        if ($stored !== $detected || $source !== 'detected') {
            $session->put(self::SESSION_KEY, $detected);
            $session->put(self::SOURCE_KEY, 'detected');
        }

        return $this->ensureSupported($detected);
    }

    /**
     * Diagnostic snapshot used by a debug route to figure out why the
     * wrong country is being detected. Returns the raw signals (with
     * the live IP) plus the final resolution.
     */
    public function diagnose(Request $request): array
    {
        return [
            'request_ip' => $request->ip(),
            'cf_ipcountry' => $request->header('CF-IPCountry'),
            'session_country' => $request->session()->get(self::SESSION_KEY),
            'session_source' => $request->session()->get(self::SOURCE_KEY),
            'from_cloudflare' => $this->fromCloudflare($request),
            'from_ip_lookup' => $this->fromIpLookup($request),
            'resolved' => $this->resolve($request),
            'supported_codes' => collect($this->supportedCountries())->pluck('country_code')->values(),
            'trusted_proxies' => 'see TrustProxies middleware — accepts X-Forwarded-* from any proxy',
        ];
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

        // Only cache successful lookups, and only for 24h. Caching
        // null would mean a one-time provider hiccup pins the visitor
        // to 'EG' for the rest of the day.
        $cached = Cache::get("geoip:{$ip}");
        if ($cached) return $cached;

        // Provider fallback chain, all HTTPS so shared-hosting
        // firewalls that block raw outbound HTTP (e.g. ea-php on
        // cPanel) still get a result. Returns the first provider
        // that succeeds; logs each failure so the chain stays
        // observable.
        foreach ($this->ipProviders() as $label => $resolver) {
            try {
                $code = $resolver($ip);
                if ($code && strlen($code) === 2 && ctype_alpha($code)) {
                    $code = strtoupper($code);
                    Cache::put("geoip:{$ip}", $code, self::CACHE_TTL);
                    return $code;
                }
            } catch (\Throwable $e) {
                Log::warning("geoip provider failed: {$label}", [
                    'ip' => $ip,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        return null;
    }

    /**
     * Provider chain — keyed by label so log messages are readable.
     * Each closure takes the IP and returns a 2-letter ISO code or
     * throws on failure. Order is fastest/most-reliable first.
     */
    protected function ipProviders(): array
    {
        return [
            // Plain JSON, no key needed, generous free tier. Fast.
            'country.is' => fn (string $ip) => Http::timeout(3)
                ->acceptJson()
                ->get("https://api.country.is/{$ip}")
                ->throw()
                ->json('country'),

            // Backup #1 — returns plain text country code on a path.
            'ipapi.co' => fn (string $ip) => trim(Http::timeout(3)
                ->withHeaders(['User-Agent' => 'Doctorato/1.0'])
                ->get("https://ipapi.co/{$ip}/country/")
                ->throw()
                ->body()),

            // Backup #2 — last resort. Returns JSON with countryCode.
            'freeipapi.com' => fn (string $ip) => Http::timeout(3)
                ->acceptJson()
                ->get("https://freeipapi.com/api/json/{$ip}")
                ->throw()
                ->json('countryCode'),
        ];
    }

    /** If a detected country has no price rows yet, fall back to EG. */
    protected function ensureSupported(string $code): string
    {
        $supported = collect($this->supportedCountries())->pluck('country_code')->toArray();
        return in_array($code, $supported, true) ? $code : 'EG';
    }
}

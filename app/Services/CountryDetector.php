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

    /**
     * True after resolve() if no server-side signal worked and we
     * fell back to the default 'EG'. The frontend reads this via
     * Inertia and runs a browser-side detection.
     */
    protected bool $serverDetectionFailed = false;

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

        // Browser detection from a previous request in this session.
        // Already trusted, no need to re-run anything.
        if ($stored && $source === 'browser') {
            return $this->ensureSupported(strtoupper($stored));
        }

        // Auto-detect from the current request. Cascade through the
        // cheapest signals first (zero-latency headers + native PHP
        // extensions) before falling back to HTTPS lookups.
        $cf = $this->fromCloudflare($request);
        $headers = $cf ?? $this->fromServerHeaders($request);
        $ext = $headers ?? $this->fromGeoipExtension($request);
        $detected = $ext ?? $this->fromIpLookup($request);

        // None of the server signals worked → flag for the frontend
        // to run browser-side detection (calls a free HTTPS geo API
        // from JavaScript, bypassing all server PHP-config issues).
        $this->serverDetectionFailed = $detected === null;
        $detected = $detected ?? 'EG';

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
        // Trim the loaded-extensions list to the ones relevant to
        // outbound HTTP so the diagnose payload stays compact.
        $extOfInterest = array_intersect(
            ['curl', 'openssl', 'geoip', 'maxminddb', 'sockets', 'mbstring'],
            get_loaded_extensions()
        );

        return [
            'php' => [
                'version' => PHP_VERSION,
                'sapi' => php_sapi_name(),
                'binary_path' => PHP_BINARY,
                'extensions_loaded' => array_values($extOfInterest),
                'curl_loaded' => extension_loaded('curl'),
                'geoip_loaded' => extension_loaded('geoip'),
                'allow_url_fopen' => (bool) ini_get('allow_url_fopen'),
            ],
            'request' => [
                'ip' => $request->ip(),
                'real_ip_header' => $request->header('X-Forwarded-For'),
                'cf_ipcountry' => $request->header('CF-IPCountry'),
                'apache_geoip_country' => $_SERVER['GEOIP_COUNTRY_CODE'] ?? null,
            ],
            'session' => [
                'country' => $request->session()->get(self::SESSION_KEY),
                'source' => $request->session()->get(self::SOURCE_KEY),
            ],
            'lookups' => [
                'from_cloudflare' => $this->fromCloudflare($request),
                'from_server_headers' => $this->fromServerHeaders($request),
                'from_geoip_extension' => $this->fromGeoipExtension($request),
                'from_ip_lookup' => $this->fromIpLookup($request),
            ],
            'resolved' => $this->resolve($request),
            'supported_codes' => collect($this->supportedCountries())->pluck('country_code')->values(),
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
     * Store a country code that came from the browser-side detector
     * (the JS fallback for hosts where server-side lookups fail).
     * Marked 'browser' so resolve() skips re-detection on the next
     * request but explicit user switches still take priority.
     */
    public function setFromBrowser(Request $request, string $code): void
    {
        $request->session()->put(self::SESSION_KEY, strtoupper($code));
        $request->session()->put(self::SOURCE_KEY, 'browser');
    }

    /** True if the most recent resolve() call had to fall back to 'EG'. */
    public function didServerDetectionFail(): bool
    {
        return $this->serverDetectionFailed;
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

    /**
     * Look for GeoIP country headers set by Apache mod_geoip2 or
     * nginx GeoIP modules. Many cPanel/WHM hosts ship one of these
     * by default — zero outbound traffic, zero latency.
     */
    protected function fromServerHeaders(Request $request): ?string
    {
        foreach (['GEOIP_COUNTRY_CODE', 'HTTP_X_GEO_COUNTRY', 'HTTP_X_COUNTRY_CODE'] as $key) {
            $value = $_SERVER[$key] ?? $request->header($key);
            if ($value && strlen($value) === 2 && ctype_alpha($value)) {
                return strtoupper($value);
            }
        }
        return null;
    }

    /**
     * Use the PHP geoip extension if it's loaded. Requires the
     * GeoIP.dat database on the server (`/usr/share/GeoIP/GeoIP.dat`
     * on most cPanel boxes). Costs ~1ms, no network.
     */
    protected function fromGeoipExtension(Request $request): ?string
    {
        if (!function_exists('geoip_country_code_by_name')) return null;
        $ip = $request->ip();
        if (!$ip) return null;
        try {
            $code = @geoip_country_code_by_name($ip);
            return ($code && strlen($code) === 2) ? strtoupper($code) : null;
        } catch (\Throwable $e) {
            return null;
        }
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

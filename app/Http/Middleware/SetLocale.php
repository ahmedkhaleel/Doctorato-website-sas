<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Resolves locale in this order:
     *   1. ?lang=ar / ?lang=en query param — lets Google's hreflang
     *      land users on the right locale and persists the choice.
     *   2. Existing session value (manual switcher).
     *   3. Accept-Language header on first visit (so an English crawler
     *      sees English content even without a query param).
     *   4. 'ar' as the home-market default.
     */
    public function handle(Request $request, Closure $next)
    {
        $allowed = ['ar', 'en'];

        $fromQuery = $request->query('lang');
        if ($fromQuery && in_array($fromQuery, $allowed, true)) {
            $request->session()->put('locale', $fromQuery);
            $locale = $fromQuery;
        } else {
            $locale = session('locale');
            if (!$locale) {
                $preferred = strtolower(substr($request->header('Accept-Language', ''), 0, 2));
                $locale = in_array($preferred, $allowed, true) ? $preferred : 'ar';
            }
        }

        app()->setLocale($locale);
        return $next($request);
    }
}

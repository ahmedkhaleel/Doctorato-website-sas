<?php

namespace App\Providers;

use App\Models\AddOn;
use App\Models\Faq;
use App\Models\PricingPlan;
use App\Models\PlanPrice;
use App\Models\Testimonial;
use App\Observers\PublicContentObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Wire the cache-busting observer to every model that feeds
        // PublicContentCache. Any admin edit immediately invalidates
        // the cached responses — no manual flushing in controllers.
        $observed = [PricingPlan::class, PlanPrice::class, Faq::class, AddOn::class, Testimonial::class];
        foreach ($observed as $model) {
            $model::observe(PublicContentObserver::class);
        }

        $this->registerRateLimiters();
    }

    /**
     * Named rate limiters for every public attack surface.
     *
     * The numeric `throttle:N,M` notation scattered through routes/web.php
     * is fine for prototyping, but it has two problems at scale:
     *   1. The IP-only key gets shared between a "send me a magic link"
     *      attack on /portal/login and a casual user browsing /demo.
     *   2. There's no central place to read "what's our policy on X" —
     *      auditing means grepping every route file.
     *
     * Each named limiter below is keyed on (email + IP) where the action
     * targets a specific identity, or on IP alone for anonymous endpoints.
     * Limits chosen to absorb real-user retries (forgot password,
     * mis-typed email) without giving attackers a useful enumeration
     * channel.
     *
     * Routes can opt-in with ->middleware('throttle:<name>'). The legacy
     * inline numeric limits still work; we'll migrate them over time.
     */
    protected function registerRateLimiters(): void
    {
        // /demo-request — anonymous + slow. Captures a single lead from
        // a real user; a bot trying to flood our sales pipeline gets
        // pruned by both this AND the honeypot/timing checks.
        RateLimiter::for('demo-submit', fn (Request $r) => [
            Limit::perMinute(3)->by($r->ip()),
            Limit::perHour(15)->by($r->ip()),
        ]);

        // /contact — slightly higher than demo because legitimate users
        // sometimes resubmit with corrections.
        RateLimiter::for('contact-submit', fn (Request $r) => [
            Limit::perMinute(5)->by($r->ip()),
            Limit::perHour(20)->by($r->ip()),
        ]);

        // /newsletter — high volume from a single IP is almost always
        // bot enumeration testing for email-existence oracle.
        RateLimiter::for('newsletter-submit', fn (Request $r) =>
            Limit::perMinute(3)->by($r->ip())
        );

        // /portal/login — magic-link issuance. KEY ON EMAIL+IP, not IP
        // alone, so a NATed office full of customers doesn't lock each
        // other out. Per-email cap prevents the email-bombing attack
        // (attacker triggers 100 magic links to a victim's inbox).
        RateLimiter::for('portal-login', fn (Request $r) => [
            Limit::perMinute(3)->by(strtolower($r->input('email', '')) . '|' . $r->ip()),
            Limit::perHour(10)->by(strtolower($r->input('email', ''))),
        ]);

        // /admin/login — password endpoint. The LoginAttemptTracker also
        // gates with a 5/15min lockout; this is the cheaper outer ring
        // that drops requests before they touch the DB.
        RateLimiter::for('admin-login', fn (Request $r) => [
            Limit::perMinute(5)->by($r->ip()),
            Limit::perMinute(3)->by(strtolower($r->input('email', ''))),
        ]);

        // /admin/2fa/verify — separate bucket because a customer mid-2FA
        // shouldn't share a counter with their failed password attempts.
        RateLimiter::for('two-factor', fn (Request $r) =>
            Limit::perMinute(5)->by($r->session()->getId())
        );

        // /webhooks/paymob — Paymob retries on non-200, so the natural
        // rate is "as fast as Paymob retries". Per-IP allow-list isn't
        // possible without curl, so we use a generous IP throttle and
        // rely on HMAC verification as the primary defence.
        RateLimiter::for('webhooks', fn (Request $r) =>
            Limit::perMinute(60)->by($r->ip())
        );

        // /healthz — monitoring tools poll aggressively. Cap higher than
        // the rest but still bounded so a runaway poller doesn't DoS.
        RateLimiter::for('healthchecks', fn (Request $r) =>
            Limit::perMinute(120)->by($r->ip())
        );
    }
}

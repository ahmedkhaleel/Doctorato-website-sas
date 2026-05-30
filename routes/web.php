<?php

use App\Http\Controllers\{HomeController, DemoRequestController, ContactController, NewsletterController, BlogController, PricingController, PageController, CheckoutController, PaymobWebhookController, SitemapController, CaseStudyController};
use Illuminate\Support\Facades\Route;

// Language switcher
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return back();
})->name('lang.switch');

// Currency switcher
Route::get('/currency/{code}', function ($code) {
    $currency = \App\Models\Currency::where('code', strtoupper($code))->where('is_active', true)->first();
    if ($currency) {
        session(['currency' => $currency->code]);
    }
    return back();
})->name('currency.switch');

// Browser-side detection fallback. The frontend calls a free HTTPS
// geo API from JavaScript (works on any host since the browser
// handles the HTTPS request, not PHP) and POSTs the result here.
// Marked 'browser' in the session so it doesn't get re-detected.
Route::post('/_set-detected-country', function (\Illuminate\Http\Request $request) {
    $code = strtoupper((string) $request->input('country', ''));
    if (preg_match('/^[A-Z]{2}$/', $code)) {
        app(\App\Services\CountryDetector::class)->setFromBrowser($request, $code);
    }
    return response()->noContent();
})->name('country.detect_client')->middleware('throttle:6,1');
// Browser detection is a one-shot per session, so 6/minute is
// generous for real users and a hard cap on anyone trying to abuse
// the session-write to spam different countries.

// Reset the explicit lock so the next request re-detects from IP.
// MUST be declared BEFORE /country/{code} or that catch-all would
// match /country/reset with $code="reset" and persist "RESET" as
// an explicit country choice (which is what kept users stuck).
Route::get('/country/reset', function (\Illuminate\Http\Request $request) {
    $detector = app(\App\Services\CountryDetector::class);
    $detector->clearExplicit($request);
    $request->session()->forget('active_country');
    // Also drop the per-IP cache entry so the next request actually
    // re-runs the provider chain instead of returning a stale 'EG'.
    \Illuminate\Support\Facades\Cache::forget('geoip:' . $request->ip());
    return redirect('/');
})->name('country.reset');

// Country switcher — persists the chosen market in session so PricingPlan
// lookups return prices for that country on every subsequent request.
// The regex constraint blocks anything that isn't a 2-letter ISO code,
// so the reset route above can't be hijacked.
Route::get('/country/{code}', function ($code, \Illuminate\Http\Request $request) {
    app(\App\Services\CountryDetector::class)->setCountry($request, strtoupper($code));
    return back();
})->where('code', '[A-Za-z]{2}')->name('country.switch');

// Debug endpoint for country detection — open in local, and on
// production it requires ?key=<random> matching APP_KEY's prefix
// so it isn't a public information leak. Useful for diagnosing
// "wrong currency" reports from travelers.
Route::get('/_debug/country', function (\Illuminate\Http\Request $request) {
    if (!app()->environment('local')) {
        $expected = substr(str_replace('base64:', '', (string) config('app.key')), 0, 12);
        if ($request->query('key') !== $expected) abort(404);
    }
    return response()->json(
        app(\App\Services\CountryDetector::class)->diagnose($request),
        200,
        [],
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
});

// Public, branded status page for customers. Cache-fronted (60s)
// so a trending link doesn't DDoS the DB during an actual incident.
Route::get('/status', [\App\Http\Controllers\StatusPageController::class, 'show'])
    ->middleware('throttle:60,1')->name('status');

// Health checks — public, throttled lightly to absorb monitors that
// poll every 30s without enabling a load-spike vector. The shallow
// endpoint deliberately skips DB / cache reads so it stays cheap
// during a database outage (a 200 here = "PHP is up").
Route::get('/healthz', [\App\Http\Controllers\HealthController::class, 'shallow'])
    ->middleware('throttle:healthchecks')->name('healthz');
Route::get('/healthz/deep', [\App\Http\Controllers\HealthController::class, 'deep'])
    ->middleware('throttle:healthchecks')->name('healthz.deep');

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-index.xml', [SitemapController::class, 'indexFile'])->name('sitemap.index');
Route::get('/sitemap-images.xml', [SitemapController::class, 'images'])->name('sitemap.images');

// Main pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/features', [PageController::class, 'features'])->name('features');
Route::get('/portals', [PageController::class, 'portals'])->name('portals');
Route::get('/dental', [PageController::class, 'dental'])->name('dental');
Route::get('/dermatology', [PageController::class, 'dermatology'])->name('dermatology');
Route::get('/pediatrics', [PageController::class, 'pediatrics'])->name('pediatrics');
Route::get('/telemedicine', [PageController::class, 'telemedicine'])->name('telemedicine');
Route::get('/solutions', [PageController::class, 'solutions'])->name('solutions');
Route::get('/technology', [PageController::class, 'technology'])->name('technology');
Route::get('/reports', [PageController::class, 'reports'])->name('reports');
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

// Country-specific landing pages — /sa, /ae, /eg, etc. Whitelisted via
// regex so we don't collide with the other single-segment routes above.
Route::get('/{countrySlug}', [\App\Http\Controllers\CountryLandingController::class, 'show'])
    ->where('countrySlug', 'sa|ae|eg|kw|qa|bh|om|jo|iq|lb|ma|us')
    ->name('country.landing');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/demo', [PageController::class, 'demo'])->name('demo');

// Self-serve trial routes were removed — the clinic system is
// provisioned manually on a separate hosting account, so a self-serve
// signup form would mislead visitors. /demo is the canonical CTA.

Route::get('/faq', [PageController::class, 'faq'])->name('faq');

// SEO landing pages — Inertia renders bilingual content with FAQ + schema.
// Targeted at high-intent keyword queries (EMR Arabic, Medical CRM, etc).
Route::get('/emr', fn () => \Inertia\Inertia::render('Emr'))->name('emr');
Route::get('/medical-crm', fn () => \Inertia\Inertia::render('MedicalCrm'))->name('medical-crm');
Route::get('/glossary', fn () => \Inertia\Inertia::render('Glossary'))->name('glossary');
Route::get('/compare', fn () => \Inertia\Inertia::render('Compare'))->name('compare');

// Legal pages — Inertia renders the bilingual content. Footer links here.
Route::get('/privacy', fn () => \Inertia\Inertia::render('Privacy'))->name('privacy');
Route::get('/terms', fn () => \Inertia\Inertia::render('Terms'))->name('terms');

// Forms
// Named rate limiters defined in AppServiceProvider::registerRateLimiters
Route::post('/demo-request', [DemoRequestController::class, 'store'])->name('demo.store')->middleware('throttle:demo-submit');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:contact-submit');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store')->middleware('throttle:newsletter-submit');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
// RSS feed — Google/Bing pull this for faster indexing of new posts.
// Must be defined BEFORE the {slug} route or '/blog/rss.xml' would be
// matched as a slug.
Route::get('/blog/rss.xml', [BlogController::class, 'rss'])->name('blog.rss');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Case Studies
Route::get('/case-studies', [CaseStudyController::class, 'index'])->name('case-studies.index');
Route::get('/case-studies/{slug}', [CaseStudyController::class, 'show'])->name('case-studies.show');

// ROI Calculator
Route::get('/roi-calculator', fn () => \Inertia\Inertia::render('RoiCalculator'))->name('roi-calculator');

// Checkout
Route::get('/checkout/{planSlug}', [CheckoutController::class, 'show'])->name('checkout.show');
// Coupon validation throttled at 6/min — generous for a real user
// who's typing and retrying, tight enough to prevent code enumeration
// (was 20/min; an attacker could brute-force 5-char codes at that rate).
Route::post('/checkout/validate-coupon', [CheckoutController::class, 'validateCoupon'])->name('checkout.validate-coupon')->middleware('throttle:6,1');
Route::post('/checkout/start', [CheckoutController::class, 'start'])->name('checkout.start')->middleware('throttle:6,1');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');

// Paymob webhook (CSRF-exempt — see bootstrap/app.php)
Route::post('/webhooks/paymob', [PaymobWebhookController::class, 'handle'])
    ->middleware('throttle:webhooks')
    ->name('webhooks.paymob');

// Customer portal — magic-link auth, separate from admin
Route::get('/portal', [\App\Http\Controllers\CustomerPortalController::class, 'showLogin'])->name('portal.login');
Route::post('/portal/login', [\App\Http\Controllers\CustomerPortalController::class, 'sendLoginLink'])
    ->middleware('throttle:portal-login')->name('portal.send-link');
Route::get('/portal/auth/{token}', [\App\Http\Controllers\CustomerPortalController::class, 'authenticate'])
    ->where('token', '[a-f0-9]{64}')->name('portal.authenticate');

Route::middleware('customer')->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\CustomerPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/invoices/{id}', [\App\Http\Controllers\CustomerPortalController::class, 'showInvoice'])
        ->where('id', '[0-9]+')->name('invoice.show');
    Route::post('/subscriptions/{id}/cancel', [\App\Http\Controllers\CustomerPortalController::class, 'cancelSubscription'])
        ->where('id', '[0-9]+')->name('subscription.cancel');
    Route::post('/subscriptions/{id}/resume', [\App\Http\Controllers\CustomerPortalController::class, 'resumeSubscription'])
        ->where('id', '[0-9]+')->name('subscription.resume');
    Route::post('/subscriptions/{id}/pause', [\App\Http\Controllers\CustomerPortalController::class, 'pauseSubscription'])
        ->where('id', '[0-9]+')->name('subscription.pause');
    Route::post('/subscriptions/{id}/unpause', [\App\Http\Controllers\CustomerPortalController::class, 'unpauseSubscription'])
        ->where('id', '[0-9]+')->name('subscription.unpause');
    Route::get('/refer', [\App\Http\Controllers\CustomerPortalController::class, 'showReferrals'])->name('refer');
    Route::get('/profile', [\App\Http\Controllers\CustomerPortalController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\CustomerPortalController::class, 'updateProfile'])->name('profile.update');
    Route::put('/preferences', [\App\Http\Controllers\CustomerPortalController::class, 'updatePreferences'])->name('preferences.update');
    Route::post('/logout', [\App\Http\Controllers\CustomerPortalController::class, 'logout'])->name('logout');
});

// Admin Auth
// Rate-limit admin auth endpoints — 5 tries per minute keeps brute-force
// attackers out while staying out of the way of real admins fat-fingering
// their password once.
Route::get('/admin/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('admin.login');
// Admin login: 3/minute by IP. The previous 5/min lets a botnet
// distributed across a /24 try 1,275 passwords in an hour against
// each address — too permissive for the highest-value entry point.
Route::post('/admin/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->middleware('throttle:admin-login');
Route::post('/admin/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

// 2FA challenge — sits OUTSIDE the auth middleware group because the
// user is half-authenticated at this point (credentials passed,
// session holds 2fa.user_id, but Auth::check() is still false).
Route::get('/admin/2fa/challenge', [\App\Http\Controllers\Admin\AuthController::class, 'showTwoFactorChallenge'])
    ->name('admin.2fa.challenge');
Route::post('/admin/2fa/verify', [\App\Http\Controllers\Admin\AuthController::class, 'verifyTwoFactor'])
    ->middleware('throttle:two-factor')->name('admin.2fa.verify');

// Admin Dashboard (Protected)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // 2FA self-service — every authenticated admin can manage their own
    Route::get('/2fa', [\App\Http\Controllers\Admin\TwoFactorController::class, 'index'])->name('2fa.index');
    Route::post('/2fa/setup', [\App\Http\Controllers\Admin\TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::post('/2fa/confirm', [\App\Http\Controllers\Admin\TwoFactorController::class, 'confirm'])->name('2fa.confirm');
    Route::post('/2fa/disable', [\App\Http\Controllers\Admin\TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::post('/2fa/recovery', [\App\Http\Controllers\Admin\TwoFactorController::class, 'regenerateRecoveryCodes'])->name('2fa.recovery');

    Route::get('/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'index'])->name('faqs.index');
    Route::post('/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'store'])->name('faqs.store')->middleware('admin.perm:faqs.manage');
    Route::put('/faqs/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'update'])->name('faqs.update')->middleware('admin.perm:faqs.manage');
    Route::delete('/faqs/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('faqs.destroy')->middleware('admin.perm:faqs.manage');

    Route::get('/plan-prices', [\App\Http\Controllers\Admin\PlanPriceController::class, 'index'])->name('plan-prices.index');
    Route::post('/plan-prices', [\App\Http\Controllers\Admin\PlanPriceController::class, 'store'])->name('plan-prices.store')->middleware('admin.perm:plans.manage');
    Route::put('/plan-prices/{price}', [\App\Http\Controllers\Admin\PlanPriceController::class, 'update'])->name('plan-prices.update')->middleware('admin.perm:plans.manage');
    Route::delete('/plan-prices/{price}', [\App\Http\Controllers\Admin\PlanPriceController::class, 'destroy'])->name('plan-prices.destroy')->middleware('admin.perm:plans.manage');

    // Plan management is gated by plans.manage (admin + super_admin roles
    // bypass automatically via User::hasPermission).
    Route::get('/plans', [\App\Http\Controllers\Admin\PlanController::class, 'index'])->name('plans.index');
    Route::post('/plans', [\App\Http\Controllers\Admin\PlanController::class, 'store'])->name('plans.store')->middleware('admin.perm:plans.manage');
    Route::put('/plans/{plan}', [\App\Http\Controllers\Admin\PlanController::class, 'update'])->name('plans.update')->middleware('admin.perm:plans.manage');
    Route::delete('/plans/{plan}', [\App\Http\Controllers\Admin\PlanController::class, 'destroy'])->name('plans.destroy')->middleware('admin.perm:plans.manage');

    Route::get('/testimonials', [\App\Http\Controllers\Admin\TestimonialController::class, 'index'])->name('testimonials.index');
    Route::post('/testimonials', [\App\Http\Controllers\Admin\TestimonialController::class, 'store'])->name('testimonials.store')->middleware('admin.perm:testimonials.manage');
    Route::put('/testimonials/{testimonial}', [\App\Http\Controllers\Admin\TestimonialController::class, 'update'])->name('testimonials.update')->middleware('admin.perm:testimonials.manage');
    Route::delete('/testimonials/{testimonial}', [\App\Http\Controllers\Admin\TestimonialController::class, 'destroy'])->name('testimonials.destroy')->middleware('admin.perm:testimonials.manage');

    Route::get('/currencies', [\App\Http\Controllers\Admin\CurrencyController::class, 'index'])->name('currencies.index');
    Route::post('/currencies', [\App\Http\Controllers\Admin\CurrencyController::class, 'store'])->name('currencies.store')->middleware('admin.perm:currencies.manage');
    Route::put('/currencies/{currency}', [\App\Http\Controllers\Admin\CurrencyController::class, 'update'])->name('currencies.update')->middleware('admin.perm:currencies.manage');
    Route::delete('/currencies/{currency}', [\App\Http\Controllers\Admin\CurrencyController::class, 'destroy'])->name('currencies.destroy')->middleware('admin.perm:currencies.manage');

    Route::get('/contacts', [\App\Http\Controllers\Admin\ContactController::class, 'index'])->name('contacts.index');
    Route::put('/contacts/{contact}/read', [\App\Http\Controllers\Admin\ContactController::class, 'markRead'])->name('contacts.read')->middleware('admin.perm:contacts.manage');
    Route::delete('/contacts/{contact}', [\App\Http\Controllers\Admin\ContactController::class, 'destroy'])->name('contacts.destroy')->middleware('admin.perm:contacts.manage');

    Route::get('/demos', [\App\Http\Controllers\Admin\DemoController::class, 'index'])->name('demos.index');
    Route::put('/demos/{demo}/status', [\App\Http\Controllers\Admin\DemoController::class, 'updateStatus'])->name('demos.status')->middleware('admin.perm:demos.manage');
    Route::post('/demos/{demo}/extend-trial', [\App\Http\Controllers\Admin\DemoController::class, 'extendTrial'])->name('demos.extend')->middleware('admin.perm:demos.manage');
    Route::post('/demos/{demo}/seen', [\App\Http\Controllers\Admin\DemoController::class, 'markReminderSeen'])->name('demos.seen');
    Route::delete('/demos/{demo}', [\App\Http\Controllers\Admin\DemoController::class, 'destroy'])->name('demos.destroy')->middleware('admin.perm:demos.manage');

    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/case-studies', [\App\Http\Controllers\Admin\CaseStudyController::class, 'index'])->name('case-studies.index');
    Route::post('/case-studies', [\App\Http\Controllers\Admin\CaseStudyController::class, 'store'])->name('case-studies.store');
    Route::put('/case-studies/{caseStudy}', [\App\Http\Controllers\Admin\CaseStudyController::class, 'update'])->name('case-studies.update');
    Route::delete('/case-studies/{caseStudy}', [\App\Http\Controllers\Admin\CaseStudyController::class, 'destroy'])->name('case-studies.destroy');

    Route::get('/blog/posts', [\App\Http\Controllers\Admin\BlogPostController::class, 'index'])->name('blog.posts.index');
    Route::post('/blog/posts', [\App\Http\Controllers\Admin\BlogPostController::class, 'store'])->name('blog.posts.store');
    Route::put('/blog/posts/{post}', [\App\Http\Controllers\Admin\BlogPostController::class, 'update'])->name('blog.posts.update');
    Route::delete('/blog/posts/{post}', [\App\Http\Controllers\Admin\BlogPostController::class, 'destroy'])->name('blog.posts.destroy');
    Route::post('/blog/upload-image', [\App\Http\Controllers\Admin\BlogPostController::class, 'uploadImage'])->name('blog.posts.upload');

    Route::get('/blog/categories', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'index'])->name('blog.categories.index');
    Route::post('/blog/categories', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'store'])->name('blog.categories.store');
    Route::put('/blog/categories/{category}', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'update'])->name('blog.categories.update');
    Route::delete('/blog/categories/{category}', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'destroy'])->name('blog.categories.destroy');

    // Settings — writes need settings.manage; reads only need dashboard.
    Route::get('/settings/tracking', [\App\Http\Controllers\Admin\SettingsController::class, 'tracking'])->name('settings.tracking')->middleware('admin.perm:settings.manage');
    Route::put('/settings/tracking', [\App\Http\Controllers\Admin\SettingsController::class, 'updateTracking'])->name('settings.tracking.update')->middleware('admin.perm:settings.manage');
    Route::get('/settings/general', [\App\Http\Controllers\Admin\SettingsController::class, 'general'])->name('settings.general')->middleware('admin.perm:settings.manage');
    Route::put('/settings/general', [\App\Http\Controllers\Admin\SettingsController::class, 'updateGeneral'])->name('settings.general.update')->middleware('admin.perm:settings.manage');
    Route::get('/settings/launch', [\App\Http\Controllers\Admin\SettingsController::class, 'launch'])->name('settings.launch')->middleware('admin.perm:settings.manage');
    Route::put('/settings/launch', [\App\Http\Controllers\Admin\SettingsController::class, 'updateLaunch'])->name('settings.launch.update')->middleware('admin.perm:settings.manage');

    Route::get('/subscriptions', [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'show'])->name('subscriptions.show');
    // Subscription cancel / payment refund are destructive + financial —
    // require an explicit permission (super_admin/admin bypass).
    Route::post('/subscriptions/{subscription}/cancel', [\App\Http\Controllers\Admin\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel')->middleware('admin.perm:plans.manage');
    Route::post('/payments/{payment}/refund', [\App\Http\Controllers\Admin\SubscriptionController::class, 'refundPayment'])->name('payments.refund')->middleware('admin.perm:plans.manage');

    Route::get('/invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/print', [\App\Http\Controllers\Admin\InvoiceController::class, 'print'])->name('invoices.print');

    Route::get('/coupons', [\App\Http\Controllers\Admin\CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [\App\Http\Controllers\Admin\CouponController::class, 'store'])->name('coupons.store');
    Route::put('/coupons/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('coupons.destroy');

    Route::get('/addons', [\App\Http\Controllers\Admin\AddOnController::class, 'index'])->name('addons.index');
    Route::post('/addons', [\App\Http\Controllers\Admin\AddOnController::class, 'store'])->name('addons.store');
    Route::put('/addons/{addon}', [\App\Http\Controllers\Admin\AddOnController::class, 'update'])->name('addons.update');
    Route::delete('/addons/{addon}', [\App\Http\Controllers\Admin\AddOnController::class, 'destroy'])->name('addons.destroy');

    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/export', [\App\Http\Controllers\Admin\ActivityLogController::class, 'export'])->name('activity-logs.export');
    Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'global'])->name('search.global');

    Route::get('/email-templates', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('email-templates.index');
    Route::put('/email-templates/{template}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])->name('email-templates.update');

    Route::get('/export/subscriptions', [\App\Http\Controllers\Admin\ExportController::class, 'subscriptions'])->name('export.subscriptions');
    Route::get('/export/invoices', [\App\Http\Controllers\Admin\ExportController::class, 'invoices'])->name('export.invoices');
    Route::get('/export/demos', [\App\Http\Controllers\Admin\ExportController::class, 'demos'])->name('export.demos');
    Route::get('/export/contacts', [\App\Http\Controllers\Admin\ExportController::class, 'contacts'])->name('export.contacts');

    // User management — lock to users.manage so a viewer can't create
    // admin accounts.
    Route::get('/users', [\App\Http\Controllers\Admin\UsersController::class, 'index'])->name('users.index')->middleware('admin.perm:users.manage');
    Route::post('/users', [\App\Http\Controllers\Admin\UsersController::class, 'store'])->name('users.store')->middleware('admin.perm:users.manage');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UsersController::class, 'update'])->name('users.update')->middleware('admin.perm:users.manage');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UsersController::class, 'destroy'])->name('users.destroy')->middleware('admin.perm:users.manage');
    Route::put('/users/{user}/toggle-active', [\App\Http\Controllers\Admin\UsersController::class, 'toggleActive'])->name('users.toggle')->middleware('admin.perm:users.manage');

    // GDPR data-subject rights (Art. 15 access + Art. 17 erasure).
    // Read + write both gated on gdpr.manage — see GdprController for
    // why erase() is irreversible and what gets preserved.
    Route::get('/gdpr', [\App\Http\Controllers\Admin\GdprController::class, 'index'])
        ->name('gdpr.index')->middleware('admin.perm:gdpr.manage');
    Route::post('/gdpr/export', [\App\Http\Controllers\Admin\GdprController::class, 'export'])
        ->name('gdpr.export')->middleware('admin.perm:gdpr.manage');
    Route::post('/gdpr/erase', [\App\Http\Controllers\Admin\GdprController::class, 'erase'])
        ->name('gdpr.erase')->middleware('admin.perm:gdpr.manage');

    // Webhook event inspector + replay. Read gated on the
    // permission too — payloads contain transaction-level data
    // that shouldn't leak to a content-only viewer role.
    Route::get('/webhooks', [\App\Http\Controllers\Admin\WebhookController::class, 'index'])
        ->name('webhooks.index')->middleware('admin.perm:webhooks.manage');
    Route::get('/webhooks/{event}', [\App\Http\Controllers\Admin\WebhookController::class, 'show'])
        ->name('webhooks.show')->middleware('admin.perm:webhooks.manage')->where('event', '[0-9]+');
    Route::post('/webhooks/{event}/replay', [\App\Http\Controllers\Admin\WebhookController::class, 'replay'])
        ->name('webhooks.replay')->middleware('admin.perm:webhooks.manage')->where('event', '[0-9]+');
});

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

// Country switcher — persists the chosen market in session so PricingPlan
// lookups return prices for that country on every subsequent request.
Route::get('/country/{code}', function ($code, \Illuminate\Http\Request $request) {
    app(\App\Services\CountryDetector::class)->setCountry($request, strtoupper($code));
    return back();
})->name('country.switch');

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

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

// Legal pages — Inertia renders the bilingual content. Footer links here.
Route::get('/privacy', fn () => \Inertia\Inertia::render('Privacy'))->name('privacy');
Route::get('/terms', fn () => \Inertia\Inertia::render('Terms'))->name('terms');

// Forms
Route::post('/demo-request', [DemoRequestController::class, 'store'])->name('demo.store')->middleware('throttle:3,1');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store')->middleware('throttle:3,1');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Case Studies
Route::get('/case-studies', [CaseStudyController::class, 'index'])->name('case-studies.index');
Route::get('/case-studies/{slug}', [CaseStudyController::class, 'show'])->name('case-studies.show');

// ROI Calculator
Route::get('/roi-calculator', fn () => \Inertia\Inertia::render('RoiCalculator'))->name('roi-calculator');

// Checkout
Route::get('/checkout/{planSlug}', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout/validate-coupon', [CheckoutController::class, 'validateCoupon'])->name('checkout.validate-coupon')->middleware('throttle:20,1');
Route::post('/checkout/start', [CheckoutController::class, 'start'])->name('checkout.start')->middleware('throttle:6,1');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');

// Paymob webhook (CSRF-exempt — see bootstrap/app.php)
Route::post('/webhooks/paymob', [PaymobWebhookController::class, 'handle'])->name('webhooks.paymob');

// Admin Auth
// Rate-limit admin auth endpoints — 5 tries per minute keeps brute-force
// attackers out while staying out of the way of real admins fat-fingering
// their password once.
Route::get('/admin/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/admin/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

// Admin Dashboard (Protected)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'index'])->name('faqs.index');
    Route::post('/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'store'])->name('faqs.store');
    Route::put('/faqs/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'update'])->name('faqs.update');
    Route::delete('/faqs/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('faqs.destroy');

    Route::get('/plan-prices', [\App\Http\Controllers\Admin\PlanPriceController::class, 'index'])->name('plan-prices.index');
    Route::post('/plan-prices', [\App\Http\Controllers\Admin\PlanPriceController::class, 'store'])->name('plan-prices.store');
    Route::put('/plan-prices/{price}', [\App\Http\Controllers\Admin\PlanPriceController::class, 'update'])->name('plan-prices.update');
    Route::delete('/plan-prices/{price}', [\App\Http\Controllers\Admin\PlanPriceController::class, 'destroy'])->name('plan-prices.destroy');

    // Plan management is gated by plans.manage (admin + super_admin roles
    // bypass automatically via User::hasPermission).
    Route::get('/plans', [\App\Http\Controllers\Admin\PlanController::class, 'index'])->name('plans.index');
    Route::post('/plans', [\App\Http\Controllers\Admin\PlanController::class, 'store'])->name('plans.store')->middleware('admin.perm:plans.manage');
    Route::put('/plans/{plan}', [\App\Http\Controllers\Admin\PlanController::class, 'update'])->name('plans.update')->middleware('admin.perm:plans.manage');
    Route::delete('/plans/{plan}', [\App\Http\Controllers\Admin\PlanController::class, 'destroy'])->name('plans.destroy')->middleware('admin.perm:plans.manage');

    Route::get('/testimonials', [\App\Http\Controllers\Admin\TestimonialController::class, 'index'])->name('testimonials.index');
    Route::post('/testimonials', [\App\Http\Controllers\Admin\TestimonialController::class, 'store'])->name('testimonials.store');
    Route::put('/testimonials/{testimonial}', [\App\Http\Controllers\Admin\TestimonialController::class, 'update'])->name('testimonials.update');
    Route::delete('/testimonials/{testimonial}', [\App\Http\Controllers\Admin\TestimonialController::class, 'destroy'])->name('testimonials.destroy');

    Route::get('/currencies', [\App\Http\Controllers\Admin\CurrencyController::class, 'index'])->name('currencies.index');
    Route::post('/currencies', [\App\Http\Controllers\Admin\CurrencyController::class, 'store'])->name('currencies.store');
    Route::put('/currencies/{currency}', [\App\Http\Controllers\Admin\CurrencyController::class, 'update'])->name('currencies.update');
    Route::delete('/currencies/{currency}', [\App\Http\Controllers\Admin\CurrencyController::class, 'destroy'])->name('currencies.destroy');

    Route::get('/contacts', [\App\Http\Controllers\Admin\ContactController::class, 'index'])->name('contacts.index');
    Route::put('/contacts/{contact}/read', [\App\Http\Controllers\Admin\ContactController::class, 'markRead'])->name('contacts.read');
    Route::delete('/contacts/{contact}', [\App\Http\Controllers\Admin\ContactController::class, 'destroy'])->name('contacts.destroy');

    Route::get('/demos', [\App\Http\Controllers\Admin\DemoController::class, 'index'])->name('demos.index');
    Route::put('/demos/{demo}/status', [\App\Http\Controllers\Admin\DemoController::class, 'updateStatus'])->name('demos.status');
    Route::post('/demos/{demo}/extend-trial', [\App\Http\Controllers\Admin\DemoController::class, 'extendTrial'])->name('demos.extend');
    Route::post('/demos/{demo}/seen', [\App\Http\Controllers\Admin\DemoController::class, 'markReminderSeen'])->name('demos.seen');
    Route::delete('/demos/{demo}', [\App\Http\Controllers\Admin\DemoController::class, 'destroy'])->name('demos.destroy');

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
});

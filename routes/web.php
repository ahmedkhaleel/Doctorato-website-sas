<?php

use App\Http\Controllers\{HomeController, DemoRequestController, ContactController, NewsletterController, BlogController, PricingController, PageController};
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

// Main pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/features', [PageController::class, 'features'])->name('features');
Route::get('/portals', [PageController::class, 'portals'])->name('portals');
Route::get('/dental', [PageController::class, 'dental'])->name('dental');
Route::get('/solutions', [PageController::class, 'solutions'])->name('solutions');
Route::get('/technology', [PageController::class, 'technology'])->name('technology');
Route::get('/reports', [PageController::class, 'reports'])->name('reports');
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/demo', [PageController::class, 'demo'])->name('demo');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

// Forms
Route::post('/demo-request', [DemoRequestController::class, 'store'])->name('demo.store')->middleware('throttle:3,1');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store')->middleware('throttle:3,1');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

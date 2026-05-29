<?php

namespace App\Providers;

use App\Models\AddOn;
use App\Models\Faq;
use App\Models\PricingPlan;
use App\Models\PlanPrice;
use App\Models\Testimonial;
use App\Observers\PublicContentObserver;
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
    }
}

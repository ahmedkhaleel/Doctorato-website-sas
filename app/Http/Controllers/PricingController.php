<?php

namespace App\Http\Controllers;

use App\Models\AddOn;
use App\Models\Currency;
use App\Models\Faq;
use App\Models\PricingPlan;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function index()
    {
        return Inertia::render('Pricing', [
            'plans' => PricingPlan::where('is_active', true)->orderBy('display_order')->get(),
            'faqs' => Faq::where('is_active', true)->where('category', 'pricing')->orderBy('display_order')->get(),
            'currencies' => Currency::where('is_active', true)->orderBy('display_order')->get(),
            'currentCurrency' => session('currency', 'EGP'),
            'addons' => AddOn::active()->orderBy('display_order')->get(),
        ]);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'currencies' => fn () => Currency::where('is_active', true)->orderBy('display_order')->get(),
            'currentCurrency' => fn () => session('currency', 'SAR'),
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    }
}

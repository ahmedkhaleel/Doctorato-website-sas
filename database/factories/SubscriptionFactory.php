<?php

namespace Database\Factories;

use App\Models\DemoRequest;
use App\Models\PricingPlan;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        return [
            'pricing_plan_id' => PricingPlan::factory(),
            'demo_request_id' => DemoRequest::factory(),
            'customer_name' => "Customer {$sequence}",
            'customer_email' => "sub+{$sequence}@example.invalid",
            'customer_phone' => '+201001234567',
            'clinic_name' => "Clinic {$sequence}",
            'country' => 'EG',
            'billing_cycle' => 'monthly',
            'amount' => 100,
            'currency' => 'EGP',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'reference' => 'SUB-' . strtoupper(Str::random(10)),
        ];
    }
}

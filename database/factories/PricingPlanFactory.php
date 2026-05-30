<?php

namespace Database\Factories;

use App\Models\PricingPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PricingPlanFactory extends Factory
{
    protected $model = PricingPlan::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        return [
            'name_ar' => "خطة {$sequence}",
            'name_en' => "Plan {$sequence}",
            'slug' => "plan-{$sequence}",
            'description_ar' => '-',
            'description_en' => '-',
            'monthly_price' => 100,
            'yearly_price' => 1000,
            'currency' => 'EGP',
            'is_popular' => false,
            'is_custom' => false,
            'is_active' => true,
            'features_ar' => [],
            'features_en' => [],
            'modules_included' => [],
            'support_level' => 'standard',
            'display_order' => $sequence,
        ];
    }
}

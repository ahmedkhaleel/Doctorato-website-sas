<?php

namespace Database\Factories;

use App\Models\PlanPrice;
use App\Models\PricingPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanPriceFactory extends Factory
{
    protected $model = PlanPrice::class;

    public function definition(): array
    {
        return [
            'pricing_plan_id' => PricingPlan::factory(),
            'country_code' => 'EG',
            'country_name_ar' => 'مصر',
            'country_name_en' => 'Egypt',
            'country_flag' => '🇪🇬',
            'currency_code' => 'EGP',
            'currency_symbol' => 'ج.م',
            'monthly_price' => 100,
            'yearly_price' => 1000,
            'is_active' => true,
        ];
    }
}

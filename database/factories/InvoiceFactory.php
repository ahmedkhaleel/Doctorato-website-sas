<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        return [
            'subscription_id' => Subscription::factory(),
            'number' => 'INV-2026-' . str_pad((string) $sequence, 5, '0', STR_PAD_LEFT),
            'subtotal' => 100,
            'tax' => 0,
            'discount' => 0,
            'total' => 100,
            'currency' => 'EGP',
            'status' => 'pending',
            'dunning_stage' => 0,
        ];
    }
}

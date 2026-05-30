<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'subscription_id' => function (array $attrs) {
                return Invoice::find($attrs['invoice_id'])?->subscription_id
                    ?? Subscription::factory()->create()->id;
            },
            'gateway' => 'paymob',
            'gateway_order_id' => 'paymob-order-' . uniqid(),
            'amount' => 100,
            'currency' => 'EGP',
            'status' => 'initiated',
        ];
    }
}

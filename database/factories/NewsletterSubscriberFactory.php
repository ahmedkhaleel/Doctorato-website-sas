<?php

namespace Database\Factories;

use App\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsletterSubscriberFactory extends Factory
{
    protected $model = NewsletterSubscriber::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        return [
            'email' => "news+{$sequence}@example.invalid",
            'is_active' => true,
            'locale' => 'ar',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\DemoRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Minimal viable DemoRequestFactory.
 *
 * The model's booted() hook auto-fills trial_started_at,
 * trial_ends_at, and trial_status so we don't need to set them
 * here — keeping the factory small means tests don't drift if the
 * trial defaults change.
 */
class DemoRequestFactory extends Factory
{
    protected $model = DemoRequest::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        return [
            'clinic_name' => "Test Clinic {$sequence}",
            'full_name' => "Test Doctor {$sequence}",
            'email' => "test+{$sequence}@example.invalid",
            'phone' => '+201001234567',
            'country_code' => '+20',
            'country' => 'EG',
            'specialty' => 'general',
            'status' => 'new',
        ];
    }
}

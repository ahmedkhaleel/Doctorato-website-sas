<?php

namespace Tests\Feature;

use App\Mail\DemoAdminNotification;
use App\Mail\DemoCustomerConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Coverage for the /demo form. This is the highest-revenue surface
 * on the site (every lead here is a paid-conversion candidate), so
 * the regression surface deserves more guard-rails than /contact.
 */
class DemoRequestTest extends TestCase
{
    use RefreshDatabase;

    protected array $validPayload = [
        'clinic_name' => 'Sunshine Dental',
        'full_name' => 'Dr. Test',
        'email' => 'doc@example.com',
        'phone' => '+971 50 123 4567',
        'country_code' => '+971',
        'country' => 'United Arab Emirates',
        'doctors_count' => '5-10',
        'specialty' => 'Dental',
        'interested_modules' => ['emr', 'billing'],
        'referral_source' => 'google',
        'notes' => 'Looking to onboard before Ramadan.',
        'hp_trap' => '',
        'form_rendered_at' => 0,
        'recaptcha_token' => '',
    ];

    public function test_valid_demo_request_creates_a_row(): void
    {
        Mail::fake();

        $response = $this->from('/demo')->post('/demo-request', $this->validPayload);

        $response->assertRedirect('/demo');
        $response->assertSessionHas('success', true);

        $this->assertDatabaseHas('demo_requests', [
            'email' => 'doc@example.com',
            'clinic_name' => 'Sunshine Dental',
            'specialty' => 'Dental',
        ]);
    }

    public function test_interested_modules_is_persisted_as_json(): void
    {
        Mail::fake();

        $this->post('/demo-request', $this->validPayload);

        // interested_modules is a JSON column; cast must round-trip
        // an array — a regression here would break the admin's view.
        $demo = \App\Models\DemoRequest::first();
        $this->assertIsArray($demo->interested_modules);
        $this->assertContains('emr', $demo->interested_modules);
        $this->assertContains('billing', $demo->interested_modules);
    }

    public function test_both_emails_are_queued_on_success(): void
    {
        Mail::fake();

        $this->post('/demo-request', $this->validPayload);

        Mail::assertQueued(DemoCustomerConfirmation::class, fn ($m) => $m->hasTo('doc@example.com'));
        Mail::assertQueued(DemoAdminNotification::class, fn ($m) => $m->hasTo('info@doctorato.com'));
    }

    public function test_missing_required_fields_are_rejected(): void
    {
        Mail::fake();

        $response = $this->from('/demo')->post('/demo-request', [
            'clinic_name' => '',
            'full_name' => '',
            'email' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['clinic_name', 'full_name', 'email', 'phone', 'country_code']);
        $this->assertDatabaseCount('demo_requests', 0);
        Mail::assertNothingQueued();
    }

    public function test_long_formatted_phone_is_accepted(): void
    {
        Mail::fake();

        // Same bug shape as /contact — phone max:20 used to reject
        // anyone who typed brackets or spaces.
        $payload = array_merge($this->validPayload, [
            'phone' => '+971 (50) 123-4567 ext. 9999',
        ]);

        $response = $this->post('/demo-request', $payload);

        $response->assertSessionHas('success', true);
    }

    public function test_honeypot_blocks_the_submission(): void
    {
        Mail::fake();

        $payload = array_merge($this->validPayload, [
            'hp_trap' => 'http://crawler.example/',
        ]);

        $response = $this->from('/demo')->post('/demo-request', $payload);

        $response->assertSessionHasErrors(['clinic_name']);
        $this->assertDatabaseCount('demo_requests', 0);
        Mail::assertNothingQueued();
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Regression guards on the throttle middleware. Each test confirms
 * that a specific public endpoint shuts down a brute-force / spam
 * attempt within the configured per-minute budget.
 *
 * RateLimiter::clear() between tests ensures one test's hits don't
 * leak into another. The keys are derived from the IP, which is
 * stable per request in the testing kit (127.0.0.1).
 */
class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('ip:127.0.0.1');
        Mail::fake();
    }

    public function test_contact_form_throttles_after_5_per_minute(): void
    {
        // The endpoint allows 5 per minute; the 6th hit should 429.
        for ($i = 0; $i < 5; $i++) {
            $r = $this->post('/contact', [
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'subject' => 'Hi',
                'message' => 'Test',
            ]);
            $this->assertLessThan(500, $r->status(), "Request {$i} should not 500");
        }

        $sixth = $this->post('/contact', [
            'name' => 'User 6',
            'email' => 'user6@example.com',
            'subject' => 'Hi',
            'message' => 'Test',
        ]);
        $sixth->assertStatus(429);
    }

    public function test_demo_form_throttles_after_3_per_minute(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/demo-request', [
                'clinic_name' => "Clinic {$i}",
                'full_name' => "Dr {$i}",
                'email' => "doc{$i}@example.com",
                'phone' => '+971501234567',
                'country_code' => '+971',
            ]);
        }

        $fourth = $this->post('/demo-request', [
            'clinic_name' => 'Clinic 4',
            'full_name' => 'Dr 4',
            'email' => 'doc4@example.com',
            'phone' => '+971501234567',
            'country_code' => '+971',
        ]);
        $fourth->assertStatus(429);
    }

    public function test_admin_login_throttles_after_3_per_minute(): void
    {
        // Tightened from 5 to 3 in the security pass to slow down
        // distributed brute-force attempts against the auth surface.
        for ($i = 0; $i < 3; $i++) {
            $this->post('/admin/login', [
                'email' => 'admin@example.com',
                'password' => "wrong-{$i}",
            ]);
        }

        $fourth = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-4',
        ]);
        $fourth->assertStatus(429);
    }
}

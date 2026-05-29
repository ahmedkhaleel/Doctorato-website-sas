<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Pins the named rate-limiter contract registered in AppServiceProvider.
 *
 * The reason we test the LIMITER REGISTRATION (not actual HTTP throttle)
 * is that Laravel's throttle middleware doesn't activate on most of
 * these endpoints in a test environment (no cache backend by default),
 * and rapid-firing HTTP requests in a feature test is flaky.
 *
 * What we actually need to guarantee: a renamed or removed limiter
 * would break routes silently — these assertions catch it at test time.
 */
class RateLimiterMatrixTest extends TestCase
{
    public function test_all_named_limiters_are_registered(): void
    {
        $expected = [
            'demo-submit',
            'contact-submit',
            'newsletter-submit',
            'portal-login',
            'admin-login',
            'two-factor',
            'webhooks',
            'healthchecks',
        ];

        foreach ($expected as $name) {
            // RateLimiter::for stores the callback under the name; if the
            // name isn't registered, limiter() returns null.
            $this->assertNotNull(
                RateLimiter::limiter($name),
                "Named rate limiter '{$name}' must be registered in AppServiceProvider"
            );
        }
    }
}

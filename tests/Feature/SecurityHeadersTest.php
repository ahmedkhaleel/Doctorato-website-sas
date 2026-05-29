<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pins the security headers contract so a future refactor of
 * SecurityHeaders middleware can't silently drop a header.
 *
 *   - All HTML responses get the full battery (HSTS, X-Frame,
 *     X-Content-Type, Referrer, Permissions, CSP).
 *   - Webhook + healthcheck endpoints are exempt so CSP can't
 *     interfere with Paymob's POST or the JSON health endpoint.
 *   - Non-HTML responses (CSV, JSON) don't get CSP because the
 *     header serves no purpose on a parsed payload.
 */
class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_html_response_carries_full_header_battery(): void
    {
        $response = $this->get('/privacy');

        $response->assertHeader('Strict-Transport-Security');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy');
        $response->assertHeader('Content-Security-Policy');

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
    }

    public function test_healthcheck_endpoint_is_exempt(): void
    {
        $response = $this->get('/healthz');
        // /healthz returns JSON — it should NOT carry CSP (the
        // middleware skips this path entirely).
        $this->assertFalse(
            $response->headers->has('Content-Security-Policy'),
            'CSP must not be set on /healthz so monitoring tools can parse the response without complaints.'
        );
    }

    public function test_permissions_policy_blocks_sensitive_apis(): void
    {
        $response = $this->get('/privacy');
        $policy = (string) $response->headers->get('Permissions-Policy', '');
        $this->assertStringContainsString('camera=()', $policy);
        $this->assertStringContainsString('microphone=()', $policy);
        $this->assertStringContainsString('geolocation=()', $policy);
        $this->assertStringContainsString('payment=()', $policy);
    }
}

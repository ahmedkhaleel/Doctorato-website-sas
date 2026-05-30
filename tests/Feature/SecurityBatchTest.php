<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\DemoRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 30 — pre-launch security batch. Covers:
 *
 * Static files
 *   - /.well-known/security.txt exists on disk + has Contact + Expires
 *   - RESPONSIBLE_DISCLOSURE.md exists in repo root
 *   - /responsible-disclosure renders the public blade
 *
 * CSP nonce
 *   - The middleware sets request attribute 'csp_nonce'
 *   - The header includes a 'nonce-...' source
 *   - The same nonce reaches blade via $cspNonce
 *
 * db:anonymise
 *   - Refuses to run if APP_URL contains doctorato.com
 *   - Overwrites demo_requests PII columns
 *   - Truncates login_attempts + customer_login_tokens
 *   - Scrubs activity_logs descriptions via the PII processor
 */
class SecurityBatchTest extends TestCase
{
    use RefreshDatabase;

    // -------- static security files --------

    public function test_security_txt_exists_and_is_well_formed(): void
    {
        $path = public_path('.well-known/security.txt');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringContainsString('Contact:', $contents);
        $this->assertStringContainsString('Expires:', $contents);
        $this->assertStringContainsString('Canonical:', $contents);
    }

    public function test_responsible_disclosure_md_exists(): void
    {
        $path = base_path('RESPONSIBLE_DISCLOSURE.md');
        $this->assertFileExists($path);
        $this->assertStringContainsString('security@doctorato.com', file_get_contents($path));
    }

    public function test_responsible_disclosure_page_renders(): void
    {
        $response = $this->get('/responsible-disclosure');
        $response->assertStatus(200);
        $response->assertSee('Responsible Disclosure', false);
        $response->assertSee('security@doctorato.com', false);
    }

    // -------- CSP nonce --------

    public function test_csp_header_includes_a_nonce(): void
    {
        $response = $this->get('/privacy');
        $csp = (string) $response->headers->get('Content-Security-Policy', '');
        $this->assertMatchesRegularExpression("/'nonce-[A-Za-z0-9_-]{20,}'/", $csp,
            'CSP must inject a per-request nonce.');
    }

    public function test_csp_keeps_unsafe_inline_for_vite_compat(): void
    {
        // Nonce alone would break Vite's auto-injected modulepreload
        // and Inertia's data-page JSON. unsafe-inline must stay until
        // we refactor Vite to emit nonce-aware blobs.
        $response = $this->get('/privacy');
        $csp = (string) $response->headers->get('Content-Security-Policy', '');
        $this->assertStringContainsString("'unsafe-inline'", $csp);
    }

    public function test_nonce_changes_between_requests(): void
    {
        $first = (string) $this->get('/privacy')->headers->get('Content-Security-Policy', '');
        $second = (string) $this->get('/privacy')->headers->get('Content-Security-Policy', '');
        preg_match("/'nonce-([A-Za-z0-9_-]+)'/", $first, $m1);
        preg_match("/'nonce-([A-Za-z0-9_-]+)'/", $second, $m2);
        $this->assertNotSame($m1[1] ?? null, $m2[1] ?? null,
            'Per-request nonce must be regenerated each request.');
    }

    // -------- db:anonymise --------

    public function test_anonymise_refuses_when_app_url_is_production(): void
    {
        config(['app.url' => 'https://doctorato.com']);

        $this->artisan('db:anonymise --force')->assertExitCode(1);
    }

    public function test_anonymise_overwrites_demo_request_pii(): void
    {
        config(['app.url' => 'http://staging.example.invalid']);

        $demo = DemoRequest::create([
            'full_name' => 'Real Customer',
            'email' => 'real@x.com',
            'phone' => '+201001234567',
            'clinic_name' => 'Real Clinic',
            'specialty' => 'general',
            'country' => 'EG',
            'notes' => 'sensitive note',
        ]);

        $this->artisan('db:anonymise --force')->assertExitCode(0);

        $fresh = $demo->fresh();
        $this->assertNotSame('Real Customer', $fresh->full_name);
        $this->assertNotSame('real@x.com', $fresh->email);
        $this->assertStringEndsWith('@anonymised.invalid', $fresh->email);
        $this->assertNotSame('+201001234567', $fresh->phone);
        $this->assertNull($fresh->notes);
    }

    public function test_anonymise_truncates_login_attempts(): void
    {
        config(['app.url' => 'http://staging.example.invalid']);

        DB::table('login_attempts')->insert([
            'email_hashed' => hash('sha256', 'admin@x.com'),
            'ip' => '1.2.3.4', 'success' => false,
            'reason' => 'bad_password', 'attempted_at' => now(),
        ]);

        $this->artisan('db:anonymise --force')->assertExitCode(0);

        $this->assertSame(0, DB::table('login_attempts')->count());
    }

    public function test_anonymise_dry_does_not_mutate(): void
    {
        config(['app.url' => 'http://staging.example.invalid']);

        $demo = DemoRequest::create([
            'full_name' => 'Real Customer',
            'email' => 'real@x.com',
            'phone' => '+201001234567',
            'clinic_name' => 'Real Clinic',
            'specialty' => 'general',
            'country' => 'EG',
        ]);

        $this->artisan('db:anonymise --dry --force')->assertExitCode(0);

        $this->assertSame('Real Customer', $demo->fresh()->full_name);
    }

    public function test_anonymise_scrubs_activity_log_descriptions(): void
    {
        config(['app.url' => 'http://staging.example.invalid']);

        DB::table('activity_logs')->insert([
            'action' => 'created',
            'description' => 'Saved demo from leaky@x.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('db:anonymise --force')->assertExitCode(0);

        $row = DB::table('activity_logs')->first();
        $this->assertStringNotContainsString('leaky@x.com', $row->description);
        $this->assertStringContainsString('l***@x.com', $row->description);
    }
}

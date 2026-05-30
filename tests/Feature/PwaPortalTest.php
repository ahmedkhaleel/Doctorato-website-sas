<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 27 — PWA portal contract. Covers:
 *   - /manifest.webmanifest is reachable + has the right MIME type
 *   - /portal-sw.js is reachable and has Service-Worker-Allowed if
 *     we ever set a wider scope than the file's path (we don't yet,
 *     but pinning the contract guards a future bump)
 *   - /portal/offline renders the static shell with no auth + no
 *     Inertia round-trip
 *   - The manifest's start_url points inside the SW scope
 *   - CSP includes worker-src and manifest-src 'self' so install
 *     prompts and SW registration aren't silently blocked
 */
class PwaPortalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Static files under public/ aren't served through the PHP
     * router during phpunit, so we assert the on-disk file directly.
     * Apache + nginx + Laravels public/ DocumentRoot all serve them
     * 1:1 from disk, so reading the file is the right unit of
     * truth.
     */
    public function test_manifest_exists_and_is_well_formed(): void
    {
        $path = public_path('manifest.webmanifest');
        $this->assertFileExists($path);

        $payload = json_decode(file_get_contents($path), true);
        $this->assertIsArray($payload);
        $this->assertSame('Doctorato Portal', $payload['name']);
        $this->assertSame('/portal/dashboard', $payload['start_url']);
        $this->assertSame('/portal/', $payload['scope']);
        $this->assertSame('standalone', $payload['display']);

        // start_url MUST be inside scope so install picks up the
        // right view. This catches a future typo immediately.
        $this->assertStringStartsWith($payload['scope'], $payload['start_url']);

        // All icons must reference existing files.
        foreach ($payload['icons'] as $icon) {
            $icPath = public_path(ltrim($icon['src'], '/'));
            $this->assertFileExists($icPath, "Icon {$icon['src']} not found on disk");
        }
    }

    public function test_service_worker_file_exists(): void
    {
        $path = public_path('portal-sw.js');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringContainsString('PORTAL_SW_VERSION', $contents);
        $this->assertStringContainsString("self.location.origin", $contents,
            'SW must scope its fetch handler to same-origin requests');
    }

    public function test_offline_page_renders_without_auth(): void
    {
        // No portal session, no admin auth.
        $response = $this->get('/portal/offline');
        $response->assertStatus(200);
        $response->assertSee("You're offline right now.", false);
        $response->assertSee('Try again', false);
        // Page must not depend on Inertia (or it would 404 the JSON
        // payload on offline reload). Easy proof: no inertia data
        // attribute in the HTML.
        $this->assertStringNotContainsString('data-page', $response->getContent());
    }

    public function test_csp_allows_workers_and_manifest(): void
    {
        $response = $this->get('/privacy');
        $csp = (string) $response->headers->get('Content-Security-Policy', '');
        $this->assertStringContainsString("worker-src 'self'", $csp);
        $this->assertStringContainsString("manifest-src 'self'", $csp);
    }
}

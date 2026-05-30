<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 31 — compliance docs + public pages. Covers:
 *
 * Repo docs
 *   - docs/DPA_TEMPLATE.md, docs/SLA.md, docs/HIPAA_STATUS.md present
 *
 * Public pages (rendered without Inertia so auditors with strict
 * CSP can read them)
 *   - /sub-processors  lists Cloudflare, Paymob, the cPanel host
 *   - /responsible-disclosure already covered in Phase 30
 *   - Both carry Cache-Control: public, max-age=3600
 */
class ComplianceDocsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dpa_template_exists(): void
    {
        $path = base_path('docs/DPA_TEMPLATE.md');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringContainsString('Data Processing Addendum', $contents);
        $this->assertStringContainsString('GDPR', $contents);
        $this->assertStringContainsString('Sub-processor', $contents);
    }

    public function test_sla_doc_exists(): void
    {
        $path = base_path('docs/SLA.md');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringContainsString('Service Level Agreement', $contents);
        $this->assertStringContainsString('99.5%', $contents);
        $this->assertStringContainsString('Sev-1', $contents);
    }

    public function test_hipaa_status_doc_exists_and_is_honest(): void
    {
        $path = base_path('docs/HIPAA_STATUS.md');
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        // The doc MUST clearly state we're not HIPAA-compliant.
        $this->assertStringContainsString('NOT HIPAA-compliant', $contents);
        $this->assertStringContainsString('Do not upload PHI', $contents);
    }

    public function test_sub_processors_page_renders_with_required_entries(): void
    {
        $response = $this->get('/sub-processors');
        $response->assertStatus(200);

        // The page MUST list at least these processors — if any one
        // of them gets removed from the page without being replaced,
        // we have a disclosure gap.
        $response->assertSee('Cloudflare', false);
        $response->assertSee('Paymob', false);
        $response->assertSee('cPanel', false);
        $response->assertSee('Google', false);
    }

    public function test_sub_processors_page_has_correct_cache_header(): void
    {
        $response = $this->get('/sub-processors');
        $header = (string) $response->headers->get('Cache-Control');
        // Symfony may reorder directives; assert both parts present.
        $this->assertStringContainsString('public', $header);
        $this->assertStringContainsString('max-age=3600', $header);
    }

    public function test_sub_processors_page_is_inertia_free(): void
    {
        // Auditors with strict CSP / no JS must be able to read the
        // page. data-page would mean Inertia owns the layout.
        $response = $this->get('/sub-processors');
        $this->assertStringNotContainsString('data-page', $response->getContent());
    }
}

<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke tests for the CSV export endpoint. The expensive cases
 * (chunkById + UTF-8 BOM correctness) are pinned here because the
 * stream-download path is genuinely easy to break when refactoring
 * the controller without realising it.
 */
class ActivityLogExportTest extends TestCase
{
    use RefreshDatabase;

    protected function adminUser(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_export_requires_authentication(): void
    {
        $response = $this->get('/admin/activity-logs/export');
        $response->assertRedirect('/admin/login');
    }

    public function test_export_returns_csv_with_utf8_bom(): void
    {
        ActivityLog::factory()->create(['action' => 'created', 'subject_label' => 'مثال عربي']);

        $response = $this->actingAs($this->adminUser())
            ->get('/admin/activity-logs/export');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        // BOM first so Excel doesn't mangle the Arabic
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('مثال عربي', $content);
    }

    public function test_export_respects_action_filter(): void
    {
        ActivityLog::factory()->create(['action' => 'created', 'subject_label' => 'KEEP']);
        ActivityLog::factory()->create(['action' => 'deleted', 'subject_label' => 'DROP']);

        $response = $this->actingAs($this->adminUser())
            ->get('/admin/activity-logs/export?action=created');

        $content = $response->streamedContent();
        $this->assertStringContainsString('KEEP', $content);
        $this->assertStringNotContainsString('DROP', $content);
    }
}

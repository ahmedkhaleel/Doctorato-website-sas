<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 22 — webhook_events trimmed by maint:prune at 180 days.
 *
 * Why 180 days: covers Paymob's 120-day chargeback window + 60-day
 * buffer for the dispute paperwork. Anything older is closed.
 */
class WebhookRetentionTest extends TestCase
{
    use RefreshDatabase;

    public function test_prune_drops_old_webhook_events(): void
    {
        DB::table('webhook_events')->insert([
            ['source' => 'paymob', 'hmac_valid' => true, 'status' => 'processed',
             'payload' => '{}', 'received_at' => now()->subDays(200)],
            ['source' => 'paymob', 'hmac_valid' => true, 'status' => 'processed',
             'payload' => '{}', 'received_at' => now()->subDays(60)],
        ]);

        $this->artisan('maint:prune')->assertExitCode(0);

        $this->assertSame(1, DB::table('webhook_events')->count(),
            'Only the 60-day-old row should survive the 180-day cut.');
    }

    public function test_prune_dry_doesnt_drop_webhook_events(): void
    {
        DB::table('webhook_events')->insert([
            'source' => 'paymob', 'hmac_valid' => true, 'status' => 'processed',
            'payload' => '{}', 'received_at' => now()->subDays(200),
        ]);

        $this->artisan('maint:prune --dry')->assertExitCode(0);

        $this->assertSame(1, DB::table('webhook_events')->count(),
            'Dry run must not delete any rows.');
    }
}

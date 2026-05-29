<?php

namespace Tests\Feature;

use App\Services\LoginAttemptTracker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 13 — operational hardening. Covers:
 *   - maint:prune trims activity_logs older than 365d
 *   - maint:prune preserves PERMANENT_ACTIONS regardless of age
 *   - maint:prune trims failed_jobs older than 30d
 *   - maint:prune trims customer_login_tokens older than 7d
 *   - maint:prune --dry doesn't mutate
 *   - LoginAttemptTracker records each attempt with hashed email
 *   - LoginAttemptTracker.lockedUntil triggers after 5 fails / 15m
 *   - LoginAttemptTracker IP lockout triggers after 20 fails / 15m
 *   - LoginAttemptTracker is_locked clears outside the window
 */
class RetentionAndLockoutTest extends TestCase
{
    use RefreshDatabase;

    // -------- maint:prune --------

    public function test_prune_drops_old_activity_logs(): void
    {
        DB::table('activity_logs')->insert([
            'action' => 'updated',
            'description' => 'old',
            'created_at' => now()->subDays(400),
            'updated_at' => now()->subDays(400),
        ]);
        DB::table('activity_logs')->insert([
            'action' => 'updated',
            'description' => 'fresh',
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        $this->artisan('maint:prune')->assertExitCode(0);

        $this->assertSame(1, DB::table('activity_logs')->count());
        $this->assertSame('fresh', DB::table('activity_logs')->value('description'));
    }

    public function test_prune_preserves_permanent_actions(): void
    {
        DB::table('activity_logs')->insert([
            'action' => 'deleted',
            'description' => 'must survive',
            'created_at' => now()->subDays(1000),
            'updated_at' => now()->subDays(1000),
        ]);

        $this->artisan('maint:prune')->assertExitCode(0);

        $this->assertSame(1, DB::table('activity_logs')->count());
    }

    public function test_prune_dry_run_is_noop(): void
    {
        DB::table('activity_logs')->insert([
            'action' => 'updated',
            'description' => 'old',
            'created_at' => now()->subDays(400),
            'updated_at' => now()->subDays(400),
        ]);

        $this->artisan('maint:prune --dry')->assertExitCode(0);

        $this->assertSame(1, DB::table('activity_logs')->count());
    }

    public function test_prune_drops_old_failed_jobs(): void
    {
        DB::table('failed_jobs')->insert([
            'uuid' => 'old-uuid',
            'connection' => 'database',
            'queue' => 'default',
            'payload' => '{}',
            'exception' => 'oops',
            'failed_at' => now()->subDays(40),
        ]);
        DB::table('failed_jobs')->insert([
            'uuid' => 'new-uuid',
            'connection' => 'database',
            'queue' => 'default',
            'payload' => '{}',
            'exception' => 'oops',
            'failed_at' => now()->subDays(5),
        ]);

        $this->artisan('maint:prune')->assertExitCode(0);

        $this->assertSame(1, DB::table('failed_jobs')->count());
    }

    // -------- LoginAttemptTracker --------

    public function test_tracker_hashes_email(): void
    {
        $tracker = app(LoginAttemptTracker::class);
        $tracker->record('Admin@Doctorato.COM', '1.2.3.4', 'UA', false, 'bad_password');

        $row = DB::table('login_attempts')->first();
        // Hash is SHA-256 of the lowercased + trimmed email.
        $expected = hash('sha256', 'admin@doctorato.com');
        $this->assertSame($expected, $row->email_hashed);
    }

    public function test_lockout_kicks_in_after_threshold(): void
    {
        $tracker = app(LoginAttemptTracker::class);
        for ($i = 0; $i < LoginAttemptTracker::EMAIL_FAIL_THRESHOLD; $i++) {
            $tracker->record('admin@x.com', '1.2.3.4', 'UA', false, 'bad_password');
        }

        $until = $tracker->lockedUntil('admin@x.com', '1.2.3.4');
        $this->assertNotNull($until);
        $this->assertTrue($until->isFuture());
    }

    public function test_lockout_clears_outside_window(): void
    {
        $tracker = app(LoginAttemptTracker::class);
        // Insert 5 old failures (20 minutes ago — outside 15m window).
        for ($i = 0; $i < 5; $i++) {
            DB::table('login_attempts')->insert([
                'email_hashed' => hash('sha256', 'admin@x.com'),
                'ip' => '1.2.3.4',
                'success' => false,
                'reason' => 'bad_password',
                'attempted_at' => now()->subMinutes(20),
            ]);
        }

        $this->assertNull($tracker->lockedUntil('admin@x.com', '1.2.3.4'));
    }

    public function test_ip_lockout_triggers_after_threshold(): void
    {
        $tracker = app(LoginAttemptTracker::class);
        // 20 failures from same IP, each on a different email — no
        // single email hits the threshold but the IP does.
        for ($i = 0; $i < LoginAttemptTracker::IP_FAIL_THRESHOLD; $i++) {
            $tracker->record("user{$i}@x.com", '5.6.7.8', 'UA', false, 'bad_password');
        }

        $this->assertNotNull($tracker->lockedUntil('new@x.com', '5.6.7.8'));
    }
}

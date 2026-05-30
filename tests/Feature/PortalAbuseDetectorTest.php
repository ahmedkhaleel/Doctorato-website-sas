<?php

namespace Tests\Feature;

use App\Models\CustomerLoginToken;
use App\Services\PortalAbuseDetector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 19 — portal abuse detection. Covers:
 *   - Token IP mismatch (different /16) flagged + stamps consumed_ip
 *   - Token IP same /16 silently allowed (mobile→wifi false-positive
 *     suppression)
 *   - Token IP exact match → no signal
 *   - Session IP rotation within /16 allowed
 *   - Session IP rotation to different /16 → check returns false
 *   - First-touch on a session records the IP without flagging
 *   - Enumeration counter increments per call
 *   - Enumeration emits signal ONCE on threshold crossing
 */
class PortalAbuseDetectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_consumption_same_ip_emits_nothing(): void
    {
        [$token, $plain] = CustomerLoginToken::issue('a@x.com', '1.2.3.4');
        $signal = app(PortalAbuseDetector::class)->onTokenConsumed($token, '1.2.3.4', 'UA');
        $this->assertNull($signal);
        $this->assertSame('1.2.3.4', $token->fresh()->consumed_ip);
    }

    public function test_token_consumption_same_prefix_emits_nothing(): void
    {
        [$token] = CustomerLoginToken::issue('a@x.com', '1.2.3.4');
        $signal = app(PortalAbuseDetector::class)->onTokenConsumed($token, '1.2.99.99', 'UA');
        $this->assertNull($signal);
    }

    public function test_token_consumption_different_prefix_flags(): void
    {
        [$token] = CustomerLoginToken::issue('a@x.com', '1.2.3.4');
        $signal = app(PortalAbuseDetector::class)->onTokenConsumed($token, '99.99.99.99', 'UA');
        $this->assertSame(PortalAbuseDetector::SIGNAL_TOKEN_IP_MISMATCH, $signal);

        $log = DB::table('activity_logs')->where('action', 'portal.abuse_signal')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('token_ip_mismatch', $log->description);
    }

    public function test_first_session_request_is_allowed_and_recorded(): void
    {
        $detector = app(PortalAbuseDetector::class);
        $ok = $detector->checkSession('sess-1', 42, '10.0.0.1');
        $this->assertTrue($ok);
        $this->assertSame('10.0.0.1', Cache::get('portal.session_ip.sess-1'));
    }

    public function test_session_ip_rotation_within_prefix_allowed(): void
    {
        $detector = app(PortalAbuseDetector::class);
        $detector->checkSession('sess-2', 42, '10.0.0.1');
        $this->assertTrue($detector->checkSession('sess-2', 42, '10.0.99.99'));
    }

    public function test_session_ip_rotation_outside_prefix_blocked(): void
    {
        $detector = app(PortalAbuseDetector::class);
        $detector->checkSession('sess-3', 42, '10.0.0.1');
        $this->assertFalse($detector->checkSession('sess-3', 42, '99.99.99.99'));

        $log = DB::table('activity_logs')->where('action', 'portal.abuse_signal')->first();
        $this->assertStringContainsString('session_hijack', $log->description);
    }

    public function test_enumeration_increments_until_threshold(): void
    {
        $detector = app(PortalAbuseDetector::class);
        for ($i = 0; $i < PortalAbuseDetector::ENUMERATION_THRESHOLD; $i++) {
            $this->assertTrue($detector->checkEnumeration(7));
        }
        // The (threshold + 1) call is the one that returns false.
        $this->assertFalse($detector->checkEnumeration(7));
    }

    public function test_enumeration_signal_emitted_once_per_window(): void
    {
        $detector = app(PortalAbuseDetector::class);
        // Push past the threshold by a lot.
        for ($i = 0; $i < PortalAbuseDetector::ENUMERATION_THRESHOLD + 5; $i++) {
            $detector->checkEnumeration(9);
        }

        $count = DB::table('activity_logs')
            ->where('action', 'portal.abuse_signal')
            ->where('description', 'like', '%enumeration%')
            ->count();

        $this->assertSame(1, $count, 'Signal must emit exactly once per window, not on every over-cap request');
    }
}

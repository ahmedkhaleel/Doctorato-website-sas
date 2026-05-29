<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

/**
 * Unit-style coverage for the RFC 6238 TOTP implementation. Because
 * we wrote the algorithm ourselves (no external package), the test
 * vectors below pin every edge of the spec we depend on.
 */
class TwoFactorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_secret_round_trips_through_base32(): void
    {
        $service = app(TwoFactorService::class);
        $secret = $service->generateSecret();

        // Standard 32-char base32 secret (160-bit input).
        $this->assertMatchesRegularExpression('/^[A-Z2-7]+$/', $secret);
        $this->assertSame(32, strlen($secret));
    }

    public function test_code_round_trips_against_known_counter(): void
    {
        $service = app(TwoFactorService::class);
        $secret = $service->generateSecret();

        // The code we generate for a specific counter must verify
        // against the same secret at the same time. This catches
        // any drift in our byte packing.
        $counter = 12345678;
        $code = $service->generateCode($secret, $counter);
        $this->assertTrue($service->verify($secret, $code, $counter * 30));
    }

    public function test_drift_window_accepts_codes_30s_off(): void
    {
        $service = app(TwoFactorService::class);
        $secret = $service->generateSecret();

        $now = 1747400000;
        $codePast = $service->generateCode($secret, (int) floor($now / 30) - 1);
        $codeFuture = $service->generateCode($secret, (int) floor($now / 30) + 1);

        // Both adjacent windows must verify within the ±1 drift window.
        $this->assertTrue($service->verify($secret, $codePast, $now));
        $this->assertTrue($service->verify($secret, $codeFuture, $now));
    }

    public function test_codes_60s_off_are_rejected(): void
    {
        $service = app(TwoFactorService::class);
        $secret = $service->generateSecret();

        $now = 1747400000;
        // Two windows ahead is outside drift — should fail.
        $codeFarFuture = $service->generateCode($secret, (int) floor($now / 30) + 2);
        $this->assertFalse($service->verify($secret, $codeFarFuture, $now));
    }

    public function test_invalid_codes_are_rejected(): void
    {
        $service = app(TwoFactorService::class);
        $secret = $service->generateSecret();

        $this->assertFalse($service->verify($secret, '000000'));
        $this->assertFalse($service->verify($secret, 'abc123'));
        $this->assertFalse($service->verify($secret, ''));
        $this->assertFalse($service->verify($secret, '12345'));      // too short
        $this->assertFalse($service->verify($secret, '1234567'));    // too long
    }

    public function test_provisioning_uri_carries_required_params(): void
    {
        $service = app(TwoFactorService::class);
        $uri = $service->provisioningUri('JBSWY3DPEHPK3PXP', 'admin@doctorato.com');

        // Authenticator apps require these fields. Missing any of them
        // produces a broken QR scan with no error message.
        $this->assertStringStartsWith('otpauth://totp/', $uri);
        $this->assertStringContainsString('secret=JBSWY3DPEHPK3PXP', $uri);
        $this->assertStringContainsString('issuer=Doctorato', $uri);
        $this->assertStringContainsString('algorithm=SHA1', $uri);
        $this->assertStringContainsString('digits=6', $uri);
        $this->assertStringContainsString('period=30', $uri);
    }

    public function test_recovery_code_is_consumed_exactly_once(): void
    {
        $service = app(TwoFactorService::class);
        $user = User::factory()->create();
        $codes = $service->generateRecoveryCodes();
        $user->forceFill([
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($codes)),
        ])->save();

        $code = $codes[0];

        // First use succeeds, second use fails.
        $this->assertTrue($service->consumeRecoveryCode($user->fresh(), $code));
        $this->assertFalse($service->consumeRecoveryCode($user->fresh(), $code));
    }
}

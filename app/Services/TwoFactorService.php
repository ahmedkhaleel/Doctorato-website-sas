<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Pure-PHP RFC 6238 TOTP implementation.
 *
 * Why inline instead of a composer package:
 *   - The production host has had recurring trouble with composer
 *     packages that pull in transitive deps (the cURL saga); a
 *     single dependency-free file is one less moving part.
 *   - The 2FA surface is small enough that the entire spec is
 *     ~60 lines of PHP. The package equivalents are 4-figure LOC
 *     because they also implement HOTP, OCRA, and key-URI parsing
 *     we don't need.
 *
 * Algorithm: HMAC-SHA1 of (counter = floor(unix_time / 30)) using
 * a base32-decoded shared secret. Output reduced to 6 digits.
 *
 * Compatibility: Works with Google Authenticator, Authy, 1Password,
 * Microsoft Authenticator, Bitwarden — all the common ones.
 *
 * Storage:
 *   user.two_factor_secret           — encrypted base32 shared secret
 *   user.two_factor_recovery_codes   — encrypted JSON of 8 single-use
 *                                       hex codes for lockout recovery
 *   user.two_factor_confirmed_at     — set only after first valid code
 *                                       so partial setups don't lock out
 */
class TwoFactorService
{
    /** Length of the shared secret in bytes before base32 (160 bits = standard). */
    protected const SECRET_BYTES = 20;

    /** Drift window in 30-second steps. ±1 allows a 30s clock skew. */
    protected const WINDOW = 1;

    /**
     * Generate a fresh base32-encoded secret for a new enrolment.
     * Does NOT persist — caller stamps it on the user after the
     * first successful verify so a partial setup doesn't lock out
     * an existing admin.
     */
    public function generateSecret(): string
    {
        return $this->base32Encode(random_bytes(self::SECRET_BYTES));
    }

    /**
     * The otpauth:// URI an authenticator app can scan as a QR code.
     * Issuer + account label are how the entry shows up in the app.
     */
    public function provisioningUri(string $secret, string $email, string $issuer = 'Doctorato'): string
    {
        $label = rawurlencode($issuer . ':' . $email);
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => 6,
            'period' => 30,
        ]);
        return "otpauth://totp/{$label}?{$params}";
    }

    /**
     * Verify a 6-digit code against the user's secret. Accepts codes
     * from the previous and next 30-second window to allow ±30s of
     * client clock skew (common with phone clocks).
     */
    public function verify(string $secret, string $code, ?int $atTime = null): bool
    {
        $code = preg_replace('/\s+/', '', $code);
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }
        $time = $atTime ?? time();
        for ($drift = -self::WINDOW; $drift <= self::WINDOW; $drift++) {
            $counter = (int) floor($time / 30) + $drift;
            if (hash_equals($this->generateCode($secret, $counter), $code)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Build the current 6-digit code for a secret. Useful for tests
     * that need to compute what the user's app would show right now.
     */
    public function generateCode(string $secret, ?int $counter = null): string
    {
        $counter = $counter ?? (int) floor(time() / 30);
        $binSecret = $this->base32Decode($secret);
        // Counter as 8-byte big-endian
        $binCounter = pack('N*', 0, $counter);
        $hash = hash_hmac('sha1', $binCounter, $binSecret, true);
        // Dynamic truncation (RFC 4226 §5.4)
        $offset = ord($hash[19]) & 0x0F;
        $value = ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF);
        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate 8 single-use recovery codes for the lockout case.
     * Each is 10 hex characters (40 bits of entropy — enough to
     * resist online guessing even without rate limiting).
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        return array_map(fn () => Str::random(10), range(1, $count));
    }

    /** Encrypted persisted secret read from the user. Returns null if 2FA isn't set up. */
    public function decryptSecret(User $user): ?string
    {
        if (!$user->two_factor_secret) return null;
        try {
            return Crypt::decryptString($user->two_factor_secret);
        } catch (\Throwable) {
            return null;
        }
    }

    /** Encrypted persisted recovery codes. Returns [] if none. */
    public function decryptRecoveryCodes(User $user): array
    {
        if (!$user->two_factor_recovery_codes) return [];
        try {
            return json_decode(Crypt::decryptString($user->two_factor_recovery_codes), true) ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    /** Check a recovery code and burn it (single-use). */
    public function consumeRecoveryCode(User $user, string $code): bool
    {
        $codes = $this->decryptRecoveryCodes($user);
        $idx = array_search($code, $codes, true);
        if ($idx === false) {
            return false;
        }
        array_splice($codes, $idx, 1);
        $user->forceFill([
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($codes)),
        ])->save();
        return true;
    }

    // ─── Base32 helpers (RFC 4648, A-Z + 2-7) ──────────────────────

    protected function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        for ($i = 0; $i < strlen($data); $i++) {
            $binary .= str_pad(decbin(ord($data[$i])), 8, '0', STR_PAD_LEFT);
        }
        $binary = str_pad($binary, ceil(strlen($binary) / 5) * 5, '0', STR_PAD_RIGHT);
        $out = '';
        foreach (str_split($binary, 5) as $chunk) {
            $out .= $alphabet[bindec($chunk)];
        }
        return $out;
    }

    protected function base32Decode(string $b32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $b32 = strtoupper(rtrim($b32, '='));
        $binary = '';
        for ($i = 0; $i < strlen($b32); $i++) {
            $pos = strpos($alphabet, $b32[$i]);
            if ($pos === false) continue;
            $binary .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }
        $out = '';
        foreach (str_split($binary, 8) as $byte) {
            if (strlen($byte) === 8) {
                $out .= chr(bindec($byte));
            }
        }
        return $out;
    }
}

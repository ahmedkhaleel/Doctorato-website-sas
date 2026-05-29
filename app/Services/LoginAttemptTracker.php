<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Records + queries login attempts. Sits next to the AuthController
 * so the lockout policy is one read, one write per attempt.
 *
 * Lockout policy (deliberately conservative):
 *   - 5 failed attempts on the SAME email within 15 minutes → lock for 30 min
 *   - 20 failed attempts from the SAME IP within 15 minutes → lock for 60 min
 *
 * A successful login does NOT clear the failure history — we want the
 * paper trail. The "is locked" check only looks at the count of
 * FAILED attempts in the window; a single success doesn't reset that
 * count by design (an attacker who finally guesses one correct password
 * shouldn't get unlimited free retries afterwards).
 *
 * The email is stored as SHA-256 to avoid keeping an enumerable list
 * of admin emails in the DB. Lookup uses the same hash function.
 */
class LoginAttemptTracker
{
    public const WINDOW_MINUTES = 15;
    public const EMAIL_FAIL_THRESHOLD = 5;
    public const EMAIL_LOCKOUT_MINUTES = 30;
    public const IP_FAIL_THRESHOLD = 20;
    public const IP_LOCKOUT_MINUTES = 60;

    public function record(string $email, ?string $ip, ?string $userAgent, bool $success, ?string $reason = null): void
    {
        DB::table('login_attempts')->insert([
            'email_hashed' => $this->hash($email),
            'ip' => $ip ? substr($ip, 0, 45) : null,
            'user_agent' => $userAgent ? substr($userAgent, 0, 255) : null,
            'success' => $success,
            'reason' => $reason ? substr($reason, 0, 32) : null,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Returns null if not locked, or a Carbon "unlock at" timestamp.
     * Per-email lockout takes priority over per-IP (so a victim whose
     * email is being probed can still see their own lockout message
     * even if their IP isn't yet at the threshold).
     */
    public function lockedUntil(string $email, ?string $ip): ?\Illuminate\Support\Carbon
    {
        $windowStart = now()->subMinutes(self::WINDOW_MINUTES);

        $emailFails = DB::table('login_attempts')
            ->where('email_hashed', $this->hash($email))
            ->where('success', false)
            ->where('attempted_at', '>=', $windowStart)
            ->count();
        if ($emailFails >= self::EMAIL_FAIL_THRESHOLD) {
            $last = DB::table('login_attempts')
                ->where('email_hashed', $this->hash($email))
                ->where('success', false)
                ->where('attempted_at', '>=', $windowStart)
                ->max('attempted_at');
            return $last
                ? \Illuminate\Support\Carbon::parse($last)->addMinutes(self::EMAIL_LOCKOUT_MINUTES)
                : null;
        }

        if ($ip) {
            $ipFails = DB::table('login_attempts')
                ->where('ip', $ip)
                ->where('success', false)
                ->where('attempted_at', '>=', $windowStart)
                ->count();
            if ($ipFails >= self::IP_FAIL_THRESHOLD) {
                $last = DB::table('login_attempts')
                    ->where('ip', $ip)
                    ->where('success', false)
                    ->where('attempted_at', '>=', $windowStart)
                    ->max('attempted_at');
                return $last
                    ? \Illuminate\Support\Carbon::parse($last)->addMinutes(self::IP_LOCKOUT_MINUTES)
                    : null;
            }
        }

        return null;
    }

    protected function hash(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }
}

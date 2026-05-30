<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Magic-link token. The plain token is returned ONCE at creation
 * (for inclusion in the email URL); only the SHA-256 hash is
 * persisted, mirroring how Laravel handles its own personal access
 * tokens.
 *
 * A clicked token writes `used_at` and stays in the row forever
 * (until the cleanup command prunes it) so reuse attempts can be
 * detected and a future "suspicious activity" feature has something
 * to read.
 */
class CustomerLoginToken extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'token_hash', 'expires_at', 'used_at', 'ip_address'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /** TTL of an unused magic link, in minutes. */
    public const LIFETIME_MINUTES = 15;

    /** Length of the plain token before hashing. 32 random bytes → 64 hex chars. */
    public const PLAIN_TOKEN_BYTES = 32;

    /**
     * Issue a fresh token for an email address. Returns [model, plainToken]
     * where the plain token MUST be sent immediately and discarded —
     * we never see it again on the server side.
     */
    public static function issue(string $email, ?string $ip = null): array
    {
        $plain = bin2hex(random_bytes(self::PLAIN_TOKEN_BYTES));
        $model = self::create([
            'email' => strtolower($email),
            'token_hash' => hash('sha256', $plain),
            'expires_at' => Carbon::now()->addMinutes(self::LIFETIME_MINUTES),
            'ip_address' => $ip,
        ]);
        return [$model, $plain];
    }

    /**
     * Look up a token by its plain value. Returns null if the token
     * doesn't match any row, has expired, or has already been used.
     */
    public static function findValid(string $plain): ?self
    {
        if (empty($plain)) return null;
        $hash = hash('sha256', $plain);
        $row = self::where('token_hash', $hash)->first();
        if (!$row) return null;
        if ($row->used_at !== null) return null;
        if ($row->expires_at && $row->expires_at->isPast()) return null;
        return $row;
    }

    /** Mark this token as consumed. After this, findValid() returns null. */
    public function consume(): void
    {
        $this->update(['used_at' => Carbon::now()]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    public const STATUS_QUEUED  = 'queued';
    public const STATUS_SENDING = 'sending';
    public const STATUS_SENT    = 'sent';
    public const STATUS_FAILED  = 'failed';

    public $timestamps = false;

    protected $fillable = [
        'mailable_class', 'subject', 'hashed_recipient', 'recipient_display',
        'status', 'message_id', 'error',
        'queued_at', 'sent_at', 'failed_at',
    ];

    protected $casts = [
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * Hash an email for storage. Lowercase + trim before hashing so
     * 'A@X.com' and 'a@x.com ' both lookup the same row.
     */
    public static function hashEmail(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }

    /**
     * Build the j***@example.com display form. Matches the same
     * shape used by PiiScrubbingProcessor so the dashboard reads
     * the same way as redacted log lines.
     */
    public static function redactEmail(string $email): string
    {
        if (!str_contains($email, '@')) return '***';
        [$local, $domain] = explode('@', $email, 2);
        $first = mb_substr($local, 0, 1) ?: '*';
        return $first . '***@' . $domain;
    }
}

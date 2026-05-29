<?php

namespace App\Logging;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * Monolog processor that redacts PII from log records BEFORE they
 * are written to disk.
 *
 * What it catches:
 *   - Email addresses     → first char + *** + domain  ("a***@example.com")
 *   - Phone numbers       → last 4 digits only          ("***1234")
 *   - Credit-card-like    → fully replaced              ("[CARD]")
 *   - Authorization headers → "[REDACTED]"
 *   - Common token keys    → "[REDACTED]"
 *
 * Why this exists:
 *   The activity log + Laravel log frequently surface customer email
 *   on the way to "saved demo request" / "magic link sent" lines.
 *   If a leaked log ever ended up in a support ticket or an external
 *   monitoring tool, those rows leak the customer roster wholesale.
 *   Hashing email in the audit table doesn't help if it's still in
 *   plaintext in the log file alongside.
 *
 * Implements ProcessorInterface so it just slots into Monolog's
 * pipeline via config/logging.php without touching call sites.
 */
class PiiScrubbingProcessor implements ProcessorInterface
{
    /** Keys whose VALUE should always be redacted regardless of content. */
    protected const REDACT_KEYS = [
        'password', 'password_confirmation', 'current_password', 'new_password',
        'secret', 'api_key', 'api_token', 'token', 'access_token', 'refresh_token',
        'recaptcha_token', 'authorization', 'x-api-key', 'cookie',
        'recovery_codes', 'two_factor_secret', 'paymob_hmac', 'paymob_api_key',
    ];

    public function __invoke(LogRecord $record): LogRecord
    {
        $message = $this->scrubString((string) $record->message);
        $context = $this->scrubArray($record->context);
        $extra = $this->scrubArray($record->extra);

        return $record->with(
            message: $message,
            context: $context,
            extra: $extra,
        );
    }

    /**
     * Walk an array recursively, redacting sensitive keys outright and
     * passing other values through the string scrubber.
     */
    protected function scrubArray(array $data): array
    {
        foreach ($data as $key => $value) {
            $lowerKey = is_string($key) ? strtolower($key) : '';
            if (in_array($lowerKey, self::REDACT_KEYS, true)) {
                $data[$key] = '[REDACTED]';
                continue;
            }
            if (is_array($value)) {
                $data[$key] = $this->scrubArray($value);
            } elseif (is_string($value)) {
                $data[$key] = $this->scrubString($value);
            }
        }
        return $data;
    }

    /**
     * Pattern-based redaction. Order matters:
     *   1. Credit card first (16 digits) before phone (could partially match).
     *   2. Email before phone — emails contain digits.
     *   3. Phone last with a tight word-boundary so log timestamps are safe.
     */
    protected function scrubString(string $value): string
    {
        // CC: 13–19 digits with optional spaces/dashes.
        $value = preg_replace('/\b(?:\d[ -]?){13,19}\b/', '[CARD]', $value) ?? $value;

        // Email — keep first char + domain so the line is still useful.
        $value = preg_replace_callback(
            '/([A-Za-z0-9._%+-])[A-Za-z0-9._%+-]*(@[A-Za-z0-9.-]+\.[A-Za-z]{2,})/',
            fn ($m) => $m[1] . '***' . $m[2],
            $value
        ) ?? $value;

        // Phone: prefer the "+countrycode form" because it's unambiguous.
        // We DON'T match bare digit runs — too many false positives on
        // IDs, timestamps, and reference numbers. Trade-off: bare local
        // numbers won't be redacted, but customer-facing phone fields
        // store the full +country form anyway.
        $value = preg_replace_callback(
            '/(?<![\w\-:T+])\+\d[\d\s\-()]{7,18}\d(?![\w\-:])/',
            function ($m) {
                $digits = preg_replace('/\D/', '', $m[0]);
                return strlen($digits) >= 8 ? '***' . substr($digits, -4) : $m[0];
            },
            $value
        ) ?? $value;

        return $value;
    }
}

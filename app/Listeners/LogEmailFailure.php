<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;

/**
 * Closes the email log state machine by capturing queue failures.
 *
 * Background: Laravel doesn't emit a `MessageFailed` event. When a
 * queued Mailable's SMTP attempt throws after `tries` exhausts, the
 * queue worker writes a row to failed_jobs and fires JobFailed
 * (this event). We unpack the job payload, recognise the
 * SendQueuedMailable shape, extract the recipient(s), and update
 * the most recent matching EmailLog row to status=failed with the
 * exception message.
 *
 * Why payload-sniffing instead of a dedicated event:
 *   - JobFailed is THE framework hook for "this never landed";
 *     adding our own event would mean wrapping every Mailable.
 *   - Defensive: every other path stays untouched. If the payload
 *     shape changes in a future Laravel, the listener falls back
 *     to a no-op (the row stays at 'sending', which the dashboard
 *     already highlights via the absence of sent_at) — never
 *     crashes the worker.
 *
 * Failure modes the listener handles silently:
 *   - The failed job isn't a mailable (the if-check at the top)
 *   - The payload JSON is malformed (try/catch)
 *   - The mailable doesn't expose a `to` (no recipient → skip)
 *   - There's no matching 'sending' EmailLog row (the worker
 *     created the failed_jobs row before MessageSending fired —
 *     possible during a database transaction rollback). In that
 *     case we create a fresh 'failed' row so the trail isn't lost.
 */
class LogEmailFailure
{
    /** Job classes that wrap a Mailable. */
    protected const MAIL_JOB_CLASSES = [
        \Illuminate\Mail\SendQueuedMailable::class,
    ];

    public function handle(JobFailed $event): void
    {
        try {
            $payload = $event->job->payload();
            $jobClass = $payload['data']['commandName'] ?? null;

            if (!$jobClass || !in_array($jobClass, self::MAIL_JOB_CLASSES, true)) {
                return;
            }

            $recipients = $this->extractRecipients($payload);
            if (empty($recipients)) {
                return;
            }

            $errorMessage = $this->shortError($event->exception?->getMessage() ?? 'unknown error');
            $mailableClass = $this->extractMailableClass($payload);

            foreach ($recipients as $email) {
                $hash = EmailLog::hashEmail($email);

                $row = EmailLog::where('hashed_recipient', $hash)
                    ->whereIn('status', [EmailLog::STATUS_QUEUED, EmailLog::STATUS_SENDING])
                    ->orderByDesc('id')
                    ->first();

                if ($row) {
                    $row->update([
                        'status' => EmailLog::STATUS_FAILED,
                        'error' => $errorMessage,
                        'failed_at' => now(),
                    ]);
                } else {
                    // No precursor row — create a tombstone so the
                    // failure is still visible in the dashboard.
                    EmailLog::create([
                        'mailable_class' => $mailableClass,
                        'hashed_recipient' => $hash,
                        'recipient_display' => EmailLog::redactEmail($email),
                        'status' => EmailLog::STATUS_FAILED,
                        'error' => $errorMessage,
                        'queued_at' => now(),
                        'failed_at' => now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Last-resort: log + swallow. A logging failure must
            // not cascade into a worker crash.
            Log::warning('LogEmailFailure: handler exception', [
                'error' => $e->getMessage(),
                'job' => $event->job?->getName() ?? null,
            ]);
        }
    }

    /**
     * Pull recipient emails out of the serialised job payload.
     * Laravel stores SendQueuedMailable as a base64-encoded
     * serialise() of the mailable, with the recipient list in the
     * mailable's `to` array.
     */
    protected function extractRecipients(array $payload): array
    {
        $command = $payload['data']['command'] ?? null;
        if (!is_string($command)) return [];

        // The payload might be a plain serialised string OR (newer
        // Laravel) prefixed with "O:..." object directly. Trying
        // unserialize on both is fine; SuppressErrors via @ since
        // a serialised payload from a different framework version
        // can throw.
        $mailable = @unserialize($command, ['allowed_classes' => false]);

        // We DON'T need the actual object — just the stringified
        // form, which contains the recipient emails near the `to`
        // property. Grep them out with a regex to avoid the security
        // surface of true unserialize.
        if (preg_match_all('/"address"[^"]*"([^"]+@[^"]+)"/', $command, $matches)) {
            return array_unique($matches[1]);
        }

        // Fallback for older serialisation styles.
        if (preg_match_all('/s:\d+:"([^"]+@[^"]+\.[a-z]{2,})"/i', $command, $matches)) {
            return array_unique($matches[1]);
        }

        return [];
    }

    protected function extractMailableClass(array $payload): ?string
    {
        $command = $payload['data']['command'] ?? '';
        if (!is_string($command)) return null;

        // The serialised SendQueuedMailable embeds the mailable
        // class as the first O:NN:"FQCN" after our wrapper.
        if (preg_match('/O:\d+:"([^"]+Mail[^"]*)"/', $command, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /** Cap the stored error at 1KB so an enormous trace doesn't blow the column. */
    protected function shortError(string $msg): string
    {
        return mb_substr($msg, 0, 1000);
    }
}

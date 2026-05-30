<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

/**
 * Tracks every outbound message that hits the SMTP transport.
 *
 *   MessageSending → mark/insert as 'sending' (handshake started)
 *   MessageSent    → mark as 'sent' + capture message-id
 *
 * Failures don't get their own framework event — the queue worker
 * catches the throw and writes to failed_jobs. We pick that up in
 * a separate QueueListener (TODO: future phase) by inspecting the
 * payload's mail::send job. For now the timeline at minimum shows
 * 'sending' without a follow-up 'sent', which is enough to spot
 * a broken SMTP path.
 *
 * Defensive design: every method is wrapped in try/catch so a
 * logging failure NEVER blocks an outbound email. The whole point
 * of the table is to help with mail debugging — it must not be
 * able to cause a mail outage.
 */
class LogEmailDelivery
{
    public function handleSending(MessageSending $event): void
    {
        try {
            foreach ($event->message->getTo() ?? [] as $to) {
                $email = $to->getAddress();
                $hash = EmailLog::hashEmail($email);

                EmailLog::create([
                    'mailable_class' => $event->data['__mailable_class'] ?? null,
                    'subject' => mb_substr((string) $event->message->getSubject(), 0, 255),
                    'hashed_recipient' => $hash,
                    'recipient_display' => EmailLog::redactEmail($email),
                    'status' => EmailLog::STATUS_SENDING,
                    'queued_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('EmailLog: handleSending failed', ['error' => $e->getMessage()]);
        }
    }

    public function handleSent(MessageSent $event): void
    {
        try {
            $messageId = $event->sent->getMessageId();
            foreach ($event->message->getTo() ?? [] as $to) {
                $email = $to->getAddress();
                $hash = EmailLog::hashEmail($email);

                // Update the most-recent 'sending' row for this
                // recipient. Multiple deliveries to the same address
                // could collide, but the "most recent first" rule
                // matches the natural send order.
                $row = EmailLog::where('hashed_recipient', $hash)
                    ->where('status', EmailLog::STATUS_SENDING)
                    ->orderByDesc('id')
                    ->first();

                if ($row) {
                    $row->update([
                        'status' => EmailLog::STATUS_SENT,
                        'message_id' => $messageId,
                        'sent_at' => now(),
                    ]);
                } else {
                    // No 'sending' precursor — direct mail flow
                    // (e.g. Mail::raw in AdminExceptionNotifier).
                    // Create a one-shot row in 'sent'.
                    EmailLog::create([
                        'mailable_class' => null,
                        'subject' => mb_substr((string) $event->message->getSubject(), 0, 255),
                        'hashed_recipient' => $hash,
                        'recipient_display' => EmailLog::redactEmail($email),
                        'status' => EmailLog::STATUS_SENT,
                        'message_id' => $messageId,
                        'queued_at' => now(),
                        'sent_at' => now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('EmailLog: handleSent failed', ['error' => $e->getMessage()]);
        }
    }
}

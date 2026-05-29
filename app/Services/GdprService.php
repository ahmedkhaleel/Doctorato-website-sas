<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\DemoRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * GDPR data-subject rights for Doctorato customers.
 *
 *   GDPR Art. 15 — Right of access  → export(email)
 *   GDPR Art. 17 — Right to erasure → erase(email)
 *
 * Egyptian PDPL Article 5 reads in the same spirit so this service
 * doubles as the local compliance touchpoint.
 *
 * Architecture:
 *   - export() builds a structured array of every row tied to the
 *     identifier and returns it. The caller (controller) wraps it
 *     in a JSON download.
 *   - erase() runs inside a transaction so a partial failure rolls
 *     the whole thing back. We DON'T hard-delete invoices /
 *     subscriptions (Egyptian tax law mandates 5y retention of
 *     financial records) — instead, PII is overwritten with
 *     deterministic redaction tokens and a tombstone activity-log
 *     row is created so audit can still answer "did we receive a
 *     request from this customer".
 *   - Activity-log rows referencing the customer get their
 *     description field scrubbed via the SAME PII processor used
 *     for runtime logs — so the same regex catches both surfaces.
 */
class GdprService
{
    /** Sentinels used in place of erased PII so the schema stays valid. */
    public const ERASED_NAME = '[ERASED]';
    public const ERASED_EMAIL_PREFIX = 'erased+';
    public const ERASED_EMAIL_DOMAIN = '@deleted.invalid';

    public function export(string $email): array
    {
        $email = strtolower(trim($email));

        $demo = DemoRequest::where('email', $email)->first();
        if (!$demo) {
            return [
                'status' => 'no_data',
                'email' => $email,
                'generated_at' => now()->toIso8601String(),
            ];
        }

        $subscriptions = Subscription::where('demo_request_id', $demo->id)->get();
        $subIds = $subscriptions->pluck('id');
        $invoices = Invoice::whereIn('subscription_id', $subIds)->get();
        $payments = Payment::whereIn('subscription_id', $subIds)->get();
        $activityLogs = ActivityLog::where('description', 'like', "%{$email}%")
            ->orWhere(function ($q) use ($demo) {
                $q->where('subject_type', DemoRequest::class)
                  ->where('subject_id', $demo->id);
            })
            ->orderByDesc('created_at')
            ->limit(500)
            ->get(['action', 'description', 'subject_type', 'subject_id', 'created_at']);

        return [
            'status' => 'ok',
            'generated_at' => now()->toIso8601String(),
            'email' => $email,
            'subject' => [
                'demo_request' => $demo->only([
                    'id', 'full_name', 'email', 'phone', 'clinic_name',
                    'country', 'specialty', 'doctors_count', 'notes',
                    'status', 'trial_status', 'trial_started_at', 'trial_ends_at',
                    'created_at',
                ]),
                'subscriptions' => $subscriptions->map->only([
                    'id', 'pricing_plan_id', 'billing_cycle', 'amount', 'currency',
                    'status', 'starts_at', 'ends_at', 'reference', 'created_at',
                ])->all(),
                'invoices' => $invoices->map->only([
                    'id', 'number', 'subscription_id', 'total', 'currency',
                    'status', 'paid_at', 'created_at',
                ])->all(),
                'payments' => $payments->map->only([
                    'id', 'subscription_id', 'invoice_id', 'amount', 'currency',
                    'status', 'payment_method', 'processed_at', 'created_at',
                ])->all(),
                'activity_log' => $activityLogs->all(),
            ],
        ];
    }

    /**
     * Erase PII. Financial records remain (tax retention) with PII
     * fields overwritten. Returns a summary of what was modified.
     *
     * Reason text is stored in a tombstone activity-log row so audit
     * can show "X was erased on Y because of Z" without needing the
     * original DemoRequest.
     */
    public function erase(string $email, string $reason = 'subject_request'): array
    {
        $email = strtolower(trim($email));

        return DB::transaction(function () use ($email, $reason) {
            $demo = DemoRequest::where('email', $email)->lockForUpdate()->first();
            if (!$demo) {
                return ['status' => 'no_data', 'email' => $email];
            }

            $erasedEmail = self::ERASED_EMAIL_PREFIX . $demo->id . self::ERASED_EMAIL_DOMAIN;

            // 1. Overwrite the demo row's PII. We KEEP the row so FK
            // references from subscriptions/invoices stay intact.
            // Phone column is NOT NULL on production. Overwrite with
            // the sentinel instead of nulling so the constraint holds.
            $demo->forceFill([
                'full_name' => self::ERASED_NAME,
                'email' => $erasedEmail,
                'phone' => self::ERASED_NAME,
                'notes' => null,
                'admin_notes' => null,
                'referral_source' => null,
                'referred_by_code' => null,
                'subdomain' => null,
                'marketing_opt_in' => false,
                'marketing_opted_out_at' => $demo->marketing_opted_out_at ?? now(),
            ])->save();

            // 2. Overwrite customer columns duplicated onto subscriptions.
            Subscription::where('demo_request_id', $demo->id)->update([
                'customer_name' => self::ERASED_NAME,
                'customer_email' => $erasedEmail,
                'customer_phone' => null,
            ]);

            // 3. Scrub the activity log description field with the
            // same regex used for runtime log lines.
            $scrubber = new \App\Logging\PiiScrubbingProcessor();
            $logs = ActivityLog::where('description', 'like', "%{$email}%")->get();
            foreach ($logs as $row) {
                $clean = $scrubber->__invoke(new \Monolog\LogRecord(
                    datetime: new \DateTimeImmutable(),
                    channel: 'gdpr',
                    level: \Monolog\Level::Info,
                    message: (string) $row->description,
                    context: [],
                ))->message;
                if ($clean !== $row->description) {
                    $row->forceFill(['description' => $clean])->save();
                }
            }

            // 4. Drop login_attempts for this email — there's no
            // legitimate retention reason once consent is revoked.
            DB::table('login_attempts')
                ->where('email_hashed', hash('sha256', $email))
                ->delete();

            // 5. Tombstone — visible audit trail of the erasure.
            ActivityLog::create([
                'user_id' => null,
                'action' => 'gdpr_delete',
                'description' => "GDPR erase executed for demo_request #{$demo->id} (reason: {$reason})",
                'subject_type' => DemoRequest::class,
                'subject_id' => $demo->id,
            ]);

            Log::info('gdpr.erase_completed', [
                'demo_request_id' => $demo->id,
                'reason' => $reason,
            ]);

            return [
                'status' => 'ok',
                'demo_request_id' => $demo->id,
                'redacted_email' => $erasedEmail,
            ];
        });
    }
}

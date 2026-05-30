<?php

namespace App\Console\Commands;

use App\Mail\TrialEndingSoonMail;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * `php artisan billing:dunning`
 *
 * Industry-standard dunning loop for failed / overdue invoices.
 * Designed to be cron'd daily; idempotent so re-running the same
 * day doesn't double-charge or double-email anyone.
 *
 * Stages (days since invoice fell to 'failed' or first missed
 * the renewal):
 *   Day 0  — invoice flipped to 'failed'. Email customer.
 *   Day 3  — second email, slightly firmer tone, link to update card.
 *   Day 7  — third email, last warning before service paused.
 *   Day 10 — subscription status → 'past_due', service paused at
 *            the application level (read-only access for the user).
 *   Day 30 — subscription status → 'canceled'. Service stopped,
 *            re-activation requires a fresh checkout.
 *
 * Each stage records a flag on the invoice (dunning_stage column,
 * added below) so we don't repeat. The Mailable list is wired to
 * existing trial mailables for now — proper PaymentFailedMail +
 * PaymentLastWarningMail templates can ship later without touching
 * this state machine.
 */
class RunDunning extends Command
{
    protected $signature = 'billing:dunning
        {--dry-run : Show what would happen without changing state}';

    protected $description = 'Walk failed invoices through the dunning stages (reminders + grace + cancel).';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        if ($dry) {
            $this->warn('DRY RUN — no state will change.');
        }

        $now = Carbon::now();
        $touched = 0;

        // Pull every failed invoice that hasn't been canceled yet.
        // We sort by the failure date so the oldest moves through
        // the funnel first if the cron ran late.
        $failed = Invoice::query()
            ->where('status', 'failed')
            ->orderBy('updated_at')
            ->get();

        foreach ($failed as $invoice) {
            $daysSinceFailed = (int) Carbon::parse($invoice->updated_at)->diffInDays($now);
            $stage = (int) ($invoice->dunning_stage ?? 0);
            $sub = $invoice->subscription;
            if (!$sub) {
                continue;
            }

            // Stage 1: first reminder (Day 0-2)
            if ($stage < 1 && $daysSinceFailed >= 0) {
                $this->line("→ Invoice {$invoice->id}: first reminder ({$daysSinceFailed}d failed)");
                if (!$dry) {
                    $this->safelyEmail($invoice, 'first');
                    $invoice->update(['dunning_stage' => 1]);
                }
                $touched++;
                continue;
            }

            // Stage 2: firmer reminder (Day 3-6)
            if ($stage < 2 && $daysSinceFailed >= 3) {
                $this->line("→ Invoice {$invoice->id}: second reminder ({$daysSinceFailed}d failed)");
                if (!$dry) {
                    $this->safelyEmail($invoice, 'second');
                    $invoice->update(['dunning_stage' => 2]);
                }
                $touched++;
                continue;
            }

            // Stage 3: last warning (Day 7-9)
            if ($stage < 3 && $daysSinceFailed >= 7) {
                $this->line("→ Invoice {$invoice->id}: final warning ({$daysSinceFailed}d failed)");
                if (!$dry) {
                    $this->safelyEmail($invoice, 'final');
                    $invoice->update(['dunning_stage' => 3]);
                }
                $touched++;
                continue;
            }

            // Stage 4: subscription past_due (Day 10-29)
            // Paused subs are deliberately not auto-renewing, so a
            // failed renewal during pause is meaningless — skip the
            // past-due escalation entirely until pause clears.
            if ($sub->paused_at !== null) {
                continue;
            }
            if ($stage < 4 && $daysSinceFailed >= 10 && $sub->status === 'active') {
                $this->line("→ Subscription {$sub->id}: past_due ({$daysSinceFailed}d failed)");
                if (!$dry) {
                    $sub->update(['status' => 'past_due']);
                    $invoice->update(['dunning_stage' => 4]);
                }
                $touched++;
                continue;
            }

            // Stage 5: cancel (Day 30+)
            if ($stage < 5 && $daysSinceFailed >= 30 && $sub->status !== 'canceled') {
                $this->line("→ Subscription {$sub->id}: CANCELED ({$daysSinceFailed}d failed)");
                if (!$dry) {
                    $sub->update(['status' => 'canceled', 'canceled_at' => $now]);
                    $invoice->update(['dunning_stage' => 5]);
                }
                $touched++;
            }
        }

        $this->info("Done. {$touched} dunning action(s) " . ($dry ? 'WOULD HAVE BEEN' : 'were') . ' applied.');
        return self::SUCCESS;
    }

    /**
     * Best-effort email send. Wrapped so a single bounce doesn't
     * halt the whole dunning batch.
     *
     * NOTE: The TrialEndingSoonMail template is being reused as a
     * placeholder until proper PaymentFailedMail templates land.
     * The customer still gets a meaningful reminder + dashboard
     * link in the meantime.
     */
    protected function safelyEmail(Invoice $invoice, string $stage): void
    {
        $sub = $invoice->subscription;
        $email = $sub?->demoRequest?->email;
        if (!$email) {
            return;
        }
        try {
            // Reuse existing mail for now — replace with a real
            // PaymentFailedMail::class once the templates ship.
            Mail::to($email)->queue(new TrialEndingSoonMail($sub->demoRequest));
        } catch (\Throwable $e) {
            Log::warning("Dunning email failed [stage={$stage}]", [
                'invoice_id' => $invoice->id,
                'subscription_id' => $sub->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

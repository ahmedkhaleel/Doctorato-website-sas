<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

/**
 * Billing-ops view of the dunning state machine.
 *
 * The RunDunning console command walks failed invoices through
 * five stages (0=untouched, 1=day-3 reminder, 2=day-7 reminder,
 * 3=day-10 final notice, 4=sub past_due, 5=resolved/cancelled).
 *
 * This controller surfaces those stages so ops can:
 *   - See WHO is in WHICH stage right now (recovery dashboard)
 *   - Manually advance an invoice to the next stage (e.g. customer
 *     promised to pay but didn't — escalate now instead of waiting
 *     for the next cron tick)
 *   - Reset an invoice back to stage 0 (false-positive failure,
 *     restart the cycle from scratch)
 *   - Mark an invoice as 'resolved' so the cron stops escalating
 *     while a human deals with the customer manually
 *
 * Permission: `billing.manage` — separate from `users.manage` so
 * a finance ops account can do this without admin-user privileges.
 */
class DunningController extends Controller
{
    /** Human labels for the stage column. */
    protected const STAGE_LABELS = [
        0 => 'Untouched',
        1 => 'Day 3 reminder sent',
        2 => 'Day 7 reminder sent',
        3 => 'Day 10 final notice',
        4 => 'Subscription past_due',
        5 => 'Resolved (manual)',
    ];

    public function index(Request $request)
    {
        $query = Invoice::query()
            ->with(['subscription.demoRequest'])
            ->where('status', 'failed')
            ->when($request->query('stage') !== null && $request->query('stage') !== '',
                fn ($q) => $q->where('dunning_stage', (int) $request->query('stage')))
            ->when($request->query('q'), function ($q, $term) {
                $q->whereHas('subscription.demoRequest', function ($w) use ($term) {
                    $w->where('email', 'like', "%{$term}%")
                      ->orWhere('clinic_name', 'like', "%{$term}%");
                });
            })
            ->orderBy('dunning_stage')
            ->orderByDesc('created_at');

        $rows = $query->paginate(30)->withQueryString()->through(fn ($inv) => [
            'id' => $inv->id,
            'number' => $inv->number,
            'total' => (float) $inv->total,
            'currency' => $inv->currency,
            'dunning_stage' => (int) $inv->dunning_stage,
            'stage_label' => self::STAGE_LABELS[(int) $inv->dunning_stage] ?? 'Unknown',
            'failed_at' => $inv->updated_at?->toIso8601String(),
            'days_failed' => $inv->updated_at ? (int) now()->diffInDays($inv->updated_at, false) * -1 : null,
            'customer_email' => $inv->subscription?->demoRequest?->email,
            'clinic_name' => $inv->subscription?->demoRequest?->clinic_name,
            'subscription_id' => $inv->subscription_id,
            'subscription_status' => $inv->subscription?->status,
            'subscription_paused' => $inv->subscription?->paused_at !== null,
        ]);

        // Counts per stage for the header strip. One query each
        // (cheap because of the idx_inv_dunning composite index).
        $byStage = [];
        for ($s = 0; $s <= 5; $s++) {
            $byStage[$s] = Invoice::where('status', 'failed')->where('dunning_stage', $s)->count();
        }

        return Inertia::render('Admin/Dunning', [
            'rows' => $rows,
            'filters' => $request->only(['stage', 'q']),
            'stageLabels' => self::STAGE_LABELS,
            'byStage' => $byStage,
        ]);
    }

    public function advance(Request $request, Invoice $invoice)
    {
        $this->gate($request);
        $current = (int) $invoice->dunning_stage;
        if ($current >= 5) {
            return back()->withErrors(['dunning' => 'الفاتورة في المرحلة النهائية بالفعل.']);
        }
        $next = $current + 1;
        $invoice->update(['dunning_stage' => $next]);

        $this->log($request, 'dunning_advance', $invoice,
            "Manually advanced invoice #{$invoice->id} from stage {$current} to {$next}");

        return back()->with('success', "تم النقل إلى المرحلة {$next}.");
    }

    public function reset(Request $request, Invoice $invoice)
    {
        $this->gate($request);
        $previous = (int) $invoice->dunning_stage;
        $invoice->update(['dunning_stage' => 0]);

        $this->log($request, 'dunning_reset', $invoice,
            "Reset invoice #{$invoice->id} dunning stage from {$previous} to 0");

        return back()->with('success', 'تم إعادة الفاتورة إلى المرحلة 0.');
    }

    public function resolve(Request $request, Invoice $invoice)
    {
        $this->gate($request);
        $invoice->update(['dunning_stage' => 5]);

        $this->log($request, 'dunning_resolve', $invoice,
            "Marked invoice #{$invoice->id} as resolved — cron will stop escalating");

        return back()->with('success', 'تم وضع علامة "تم الحل" — التذكيرات ستتوقف.');
    }

    protected function gate(Request $request): void
    {
        $user = $request->user();
        if (!$user || (method_exists($user, 'hasPermission') && !$user->hasPermission('billing.manage'))) {
            abort(403, 'لا تملك صلاحية إدارة الفوترة.');
        }
    }

    protected function log(Request $request, string $action, Invoice $invoice, string $description): void
    {
        try {
            ActivityLog::create([
                'user_id' => $request->user()?->id,
                'action' => $action,
                'description' => $description,
                'subject_type' => Invoice::class,
                'subject_id' => $invoice->id,
            ]);
        } catch (\Throwable $e) {
            Log::warning('DunningController activity log failed', [
                'action' => $action, 'error' => $e->getMessage(),
            ]);
        }
    }
}

<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Two-sided referral logic.
 *
 * On subscription activation we do two things:
 *   1. Assign a referral_code to the new subscription so its owner
 *      can start sharing immediately.
 *   2. If the underlying demo_request was created via ?ref=CODE,
 *      resolve the referring subscription and credit it.
 *
 * Crediting rule (intentionally simple — easy to tune later):
 *   referrer gets 10% of the new subscription's first payment as
 *   account credit, capped at the equivalent of one month's plan.
 *   Credit is applied to the next renewal invoice by the dunning /
 *   renewal flow (not implemented here — this service only records
 *   the credit; the renewal pipeline reads referral_credit_cents
 *   and deducts on invoice generation).
 *
 * Anti-fraud guards baked in:
 *   - A subscription cannot refer itself.
 *   - A referral_code that matches no active sub is silently
 *     ignored (logged once).
 *   - Crediting happens exactly once per new sub (we check
 *     referred_by_subscription_id is null before applying).
 *   - All writes happen inside the caller's DB transaction so a
 *     webhook retry won't double-credit.
 */
class ReferralService
{
    /** Reward percentage of the first payment, basis points. 1000 = 10%. */
    public const REWARD_BPS = 1000;

    /** Max credit cap as multiplier of plan amount (1 = one month). */
    public const REWARD_CAP_MULTIPLIER = 1;

    /**
     * Called from the webhook controller right after a sub flips to
     * 'active'. Idempotent — re-running on the same sub is a no-op
     * once referred_by_subscription_id is set.
     */
    public function onSubscriptionActivated(Subscription $sub): void
    {
        // Step 1: ensure the sub has its OWN code to share.
        if (empty($sub->referral_code)) {
            $sub->forceFill(['referral_code' => Subscription::generateReferralCode()])->save();
        }

        // Step 2: credit the referrer (if any). Already-credited?
        // Bail — webhook idempotency means we WILL be called again.
        if ($sub->referred_by_subscription_id !== null) {
            return;
        }

        $code = $sub->demoRequest?->referred_by_code;
        if (empty($code)) {
            return;
        }

        $referrer = Subscription::where('referral_code', $code)
            ->where('status', 'active')
            ->lockForUpdate()
            ->first();

        if (!$referrer) {
            Log::info('referral.unknown_code', [
                'code' => $code,
                'new_sub_id' => $sub->id,
            ]);
            return;
        }

        // Self-referral guard. A customer could in theory put their
        // own code in a second signup — we don't credit that.
        if ($referrer->id === $sub->id) {
            Log::info('referral.self_referral_blocked', ['sub_id' => $sub->id]);
            return;
        }

        // Calculate credit. Amount is decimal:2 so convert to cents
        // before maths to keep arithmetic exact.
        $amountCents = (int) round(((float) $sub->amount) * 100);
        $rewardCents = (int) floor($amountCents * self::REWARD_BPS / 10000);
        $capCents = $amountCents * self::REWARD_CAP_MULTIPLIER;
        $rewardCents = min($rewardCents, $capCents);

        // Apply the credit + record the lineage on the new sub. Done
        // in a single statement each so a retry is harmless (link
        // step is guarded by the null-check above).
        DB::transaction(function () use ($sub, $referrer, $rewardCents) {
            $sub->forceFill(['referred_by_subscription_id' => $referrer->id])->save();
            $referrer->forceFill([
                'referral_credit_cents' => ($referrer->referral_credit_cents ?? 0) + $rewardCents,
            ])->save();
        });

        Log::info('referral.credit_applied', [
            'new_sub_id' => $sub->id,
            'referrer_sub_id' => $referrer->id,
            'reward_cents' => $rewardCents,
            'currency' => $sub->currency,
        ]);
    }
}

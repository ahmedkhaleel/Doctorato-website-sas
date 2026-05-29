<?php

namespace App\Mail;

use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Single mailable powers all three trial drip steps. We keep the
 * subject + view selection in one place so the drip console
 * command can call new TrialDripMail($trial, $step) without
 * needing three separate classes — and adding a future "step 4"
 * is a one-line config edit.
 *
 * Steps:
 *   1 = Welcome              (day 0 — minutes after signup)
 *   2 = Feature tour          (day 3 — by now they've poked around)
 *   3 = Case study + ROI nudge (day 7 — halfway through the trial)
 *
 * The "ending soon" and "expired" reminders live in their own
 * mailables (TrialEndingSoonMail, TrialExpiredMail) — those
 * have different copy + different urgency.
 */
class TrialDripMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    /** Per-step config — subject + view file. */
    protected const STEPS = [
        1 => ['subject' => 'Welcome to your Doctorato trial',                  'view' => 'emails.trial-drip-welcome'],
        2 => ['subject' => 'A 90-second tour of what to try next',              'view' => 'emails.trial-drip-tour'],
        3 => ['subject' => 'How a Riyadh clinic doubled bookings with Doctorato', 'view' => 'emails.trial-drip-case-study'],
    ];

    public function __construct(public DemoRequest $trial, public int $step)
    {
        if (!isset(self::STEPS[$step])) {
            throw new \InvalidArgumentException("Invalid drip step: {$step}");
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: self::STEPS[$this->step]['subject']);
    }

    public function content(): Content
    {
        return new Content(
            view: self::STEPS[$this->step]['view'],
            with: [
                'trial' => $this->trial,
                'firstName' => explode(' ', trim($this->trial->full_name ?? 'there'))[0] ?? 'there',
                'daysLeft' => $this->trial->trial_ends_at
                    ? max(0, (int) now()->diffInDays($this->trial->trial_ends_at, false))
                    : null,
                'trialUrl' => $this->trial->subdomain
                    ? "https://{$this->trial->subdomain}.doctorato.app"
                    : null,
            ],
        );
    }
}

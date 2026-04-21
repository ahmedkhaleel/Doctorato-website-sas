<?php

namespace App\Mail;

use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * "Your trial ends soon" — fired 3 days before the trial expires.
 * Primary goal: convert free users before the switch flips. Reminds
 * them of what's at stake (their data, configured workflow) and
 * points to /pricing with the launch offer still in play.
 */
class TrialEndingSoonMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DemoRequest $trial)
    {
    }

    public function envelope(): Envelope
    {
        $days = $this->trial->trial_days_left;
        return new Envelope(
            subject: "⏳ باقي {$days} أيام على انتهاء تجربتك — {$this->trial->clinic_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trial-ending-soon',
            with: [
                'trial' => $this->trial,
                'days_left' => $this->trial->trial_days_left,
                'trial_url' => $this->trial->subdomain
                    ? "https://{$this->trial->subdomain}.doctorato.app"
                    : null,
            ],
        );
    }
}

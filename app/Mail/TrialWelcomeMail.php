<?php

namespace App\Mail;

use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Welcome email sent when a clinic completes the self-serve trial
 * signup. Contains their subdomain URL, trial duration, and a tiny
 * onboarding checklist so the user doesn't have to guess what to do
 * with the temporary credentials.
 */
class TrialWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DemoRequest $trial)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎉 تجربتك المجانية جاهزة — {$this->trial->clinic_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trial-welcome',
            with: [
                'trial' => $this->trial,
                'trial_url' => $this->trial->subdomain
                    ? "https://{$this->trial->subdomain}.doctorato.app"
                    : null,
            ],
        );
    }
}

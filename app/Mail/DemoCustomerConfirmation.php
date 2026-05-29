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
 * Auto-reply for /demo requests — confirms the demo booking and
 * sets expectations: a sales rep will reach out within one
 * business hour to schedule the live walkthrough. Queued so the
 * form responds in <200ms instead of waiting for SMTP.
 */
class DemoCustomerConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public DemoRequest $demo)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your demo request received — Doctorato',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.demo-customer-confirmation',
            with: ['demo' => $this->demo],
        );
    }
}

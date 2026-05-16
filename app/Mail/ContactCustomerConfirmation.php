<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Auto-reply sent to the visitor right after they submit /contact.
 * Confirms receipt and reassures them about the SLA (1 business hour).
 */
class ContactCustomerConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $message)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your message — Doctorato',
        );
    }

    public function content(): Content
    {
        // NOTE: variable name CANNOT be 'message' — Laravel's mail
        // pipeline injects its own $message (Illuminate\Mail\Message)
        // into every view and our variable would clobber it. Use
        // 'contact' instead so the blade reads `$contact->name`.
        return new Content(
            view: 'emails.contact-customer-confirmation',
            with: ['contact' => $this->message],
        );
    }
}

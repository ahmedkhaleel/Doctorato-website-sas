<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Admin notification — fires whenever a /contact form is submitted.
 * Queued so the HTTP request returns immediately. ReplyTo is set to
 * the visitor's email so admin can hit reply and answer directly.
 */
class ContactAdminNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public ContactMessage $message)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New contact form submission — ' . $this->message->name,
            replyTo: [new Address($this->message->email, $this->message->name)],
        );
    }

    public function content(): Content
    {
        // 'contact' not 'message' — see ContactCustomerConfirmation
        // for why ($message conflicts with the built-in Illuminate
        // Mail message instance the view system injects).
        return new Content(
            view: 'emails.contact-admin-notification',
            with: ['contact' => $this->message],
        );
    }
}

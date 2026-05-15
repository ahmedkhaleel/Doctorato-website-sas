<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Admin notification — fires whenever a /contact form is submitted.
 * Sets ReplyTo to the visitor's email so admin can hit reply and
 * answer the lead directly from their inbox.
 */
class ContactAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $message)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📩 رسالة جديدة من ' . $this->message->name,
            replyTo: [new Address($this->message->email, $this->message->name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-admin-notification',
            with: ['message' => $this->message],
        );
    }
}

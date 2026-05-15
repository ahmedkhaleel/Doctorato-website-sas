<?php

namespace App\Mail;

use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Admin notification — fires whenever /demo form is submitted.
 * Includes all qualification fields so sales can call prepared.
 * ReplyTo set to the lead's email for one-click reply.
 */
class DemoAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DemoRequest $demo)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎯 طلب ديمو جديد — ' . $this->demo->clinic_name,
            replyTo: [new Address($this->demo->email, $this->demo->full_name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.demo-admin-notification',
            with: ['demo' => $this->demo],
        );
    }
}

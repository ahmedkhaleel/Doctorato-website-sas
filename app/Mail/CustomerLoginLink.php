<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * One-time login link emailed to a customer who entered their email
 * on /portal. The plain token is embedded in the URL; clicking the
 * URL hits /portal/auth/{token} which consumes the token and
 * promotes the session.
 */
class CustomerLoginLink extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public string $email, public string $link, public int $expiresInMinutes = 15)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Doctorato login link',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer-login-link',
            with: [
                'email' => $this->email,
                'link' => $this->link,
                'expires' => $this->expiresInMinutes,
            ],
        );
    }
}

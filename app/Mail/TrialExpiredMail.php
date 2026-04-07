<?php

namespace App\Mail;

use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DemoRequest $demo, public bool $forAdmin = false)
    {
    }

    public function envelope(): Envelope
    {
        $subject = $this->forAdmin
            ? "تذكير: انتهت النسخة التجريبية للعميل {$this->demo->clinic_name}"
            : 'انتهت فترتك التجريبية مع دكتوراتو';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trial-expired',
            with: [
                'demo' => $this->demo,
                'forAdmin' => $this->forAdmin,
            ],
        );
    }
}

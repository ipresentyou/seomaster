<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class TrialExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Subscription $subscription
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Deine SEOmaster-Testphase ist vorbei – wie war dein Eindruck?'
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.trial.expired', with: [
            'feedbackUrl' => URL::temporarySignedRoute('trial-feedback.show', now()->addDays(30), [
                'subscription' => $this->subscription->id,
            ]),
        ]);
    }

    public function attachments(): array
    {
        return [];
    }
}

<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RenewalReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Subscription $subscription,
        public readonly int          $daysAhead
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📅 Dein SEOmaster-Abonnement verlängert sich in {$this->daysAhead} Tagen"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.subscription.renewal-reminder');
    }

    public function attachments(): array
    {
        return [];
    }
}

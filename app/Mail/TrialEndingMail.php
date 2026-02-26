<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialEndingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Subscription $subscription,
        public readonly int          $daysLeft
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->daysLeft === 1
            ? '⏰ Dein SEOmaster-Trial endet morgen'
            : "⏳ Noch {$this->daysLeft} Tage: Dein SEOmaster-Trial läuft ab";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.trial.ending');
    }

    public function attachments(): array
    {
        return [];
    }
}

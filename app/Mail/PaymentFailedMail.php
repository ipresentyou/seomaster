<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\SubscriptionInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly SubscriptionInvoice $invoice,
        public readonly int                 $daysSinceFailed
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->daysSinceFailed >= 7
            ? '🔴 Letzte Mahnung: Zahlung für SEOmaster fehlgeschlagen'
            : '⚠️ Zahlung fehlgeschlagen – Bitte Zahlungsmethode aktualisieren';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment.failed');
    }

    public function attachments(): array
    {
        return [];
    }
}

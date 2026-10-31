<?php

namespace App\Mail;

use App\Models\TrialFeedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialFeedbackReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public TrialFeedback $feedback) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Neues SEOmaster-Feedback: ' . $this->feedback->rating . '/5');
    }

    public function content(): Content
    {
        return new Content(text: 'emails.trial.feedback-received');
    }
}

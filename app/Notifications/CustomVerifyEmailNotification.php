<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmailNotification extends BaseVerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('E-Mail-Adresse bestätigen – SEOmaster')
            ->markdown('emails.auth.verify-email', [
                'verificationUrl' => $this->verificationUrl($notifiable),
            ]);
    }
}

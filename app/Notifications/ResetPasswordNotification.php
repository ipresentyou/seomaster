<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Passwort zurücksetzen – SEOmaster')
            ->markdown('emails.auth.reset-password', [
                'resetUrl' => $this->resetUrl($notifiable),
                'countdownMinutes' => 60,
            ]);
    }
}

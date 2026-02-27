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
            ->greeting('Hallo!')
            ->line('Sie erhalten diese E-Mail, weil wir eine Anfrage zum Zurücksetzen Ihres Passworts erhalten haben.')
            ->action('Passwort zurücksetzen', $this->resetUrl($notifiable))
            ->line('Dieser Link ist 60 Minuten gültig.')
            ->line('Falls Sie kein Passwort-Reset angefordert haben, ist keine weitere Aktion erforderlich.')
            ->salutation('Mit freundlichen Grüßen, SEOmaster');
    }
}

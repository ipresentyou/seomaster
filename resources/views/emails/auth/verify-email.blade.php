@component('mail::message')
# E-Mail-Adresse bestätigen

Hallo!

Vielen Dank für deine Registrierung bei SEOmaster. Bitte klicke auf den Button unten, um deine E-Mail-Adresse zu bestätigen.

@component('mail::button', ['url' => $verificationUrl])
E-Mail bestätigen
@endcomponent

Wenn du kein Konto erstellt hast, ist keine weitere Aktion erforderlich.

Mit freundlichen Grüßen,<br>
SEOmaster Team

@component('mail::subcopy')
Wenn du Probleme beim Klicken auf den "E-Mail bestätigen" Button hast, kopiere die folgende URL und füge sie in deinen Webbrowser ein:
{{ $verificationUrl }}
@endcomponent
@endcomponent

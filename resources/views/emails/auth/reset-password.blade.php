@component('mail::message')
# Passwort zurücksetzen

Hallo!

Sie erhalten diese E-Mail, weil wir eine Anfrage zum Zurücksetzen Ihres Passworts für Ihren SEOmaster Account erhalten haben.

@component('mail::button', ['url' => $resetUrl])
Passwort zurücksetzen
@endcomponent

Dieser Link ist {{ $countdownMinutes }} Minuten gültig.

Falls Sie kein Passwort-Reset angefordert haben, ist keine weitere Aktion erforderlich.

Mit freundlichen Grüßen,<br>
SEOmaster Team
@endcomponent

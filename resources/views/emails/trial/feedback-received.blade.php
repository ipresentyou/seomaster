Neues Feedback zur SEOmaster-Testphase

Name: {{ $feedback->subscription->user->name ?? 'Gelöschtes Konto' }}
E-Mail: {{ $feedback->subscription->user->email ?? '–' }}
Bewertung: {{ $feedback->rating }} von 5
Grund: {{ \App\Models\TrialFeedback::REASONS[$feedback->reason] ?? $feedback->reason }}

Kommentar:
{{ $feedback->comment ?: 'Kein Kommentar hinterlassen.' }}

Eingegangen: {{ $feedback->created_at->timezone('Europe/Berlin')->format('d.m.Y H:i') }} Uhr

Alle Antworten im Adminbereich:
{{ url('/admin/trial-feedbacks') }}

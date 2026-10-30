<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Feedback zur Testphase | SEOmaster</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 32px 16px; background: #f8f9fa; color: #202124; font: 16px/1.6 system-ui, sans-serif; }
        main { max-width: 620px; margin: auto; padding: clamp(20px, 5vw, 40px); background: white; border: 1px solid #dadce0; border-radius: 12px; }
        h1 { font-size: 28px; line-height: 1.25; }
        p { color: #5f6368; }
        label, legend { display: block; font-weight: 600; margin: 20px 0 8px; }
        fieldset { border: 0; padding: 0; margin: 0; }
        .ratings { display: flex; flex-wrap: wrap; gap: 12px; }
        .ratings label { margin: 0; padding: 10px; border: 1px solid #dadce0; border-radius: 6px; cursor: pointer; }
        select, textarea { width: 100%; padding: 12px; border: 1px solid #80868b; border-radius: 6px; font: inherit; background: white; }
        button { margin-top: 24px; padding: 12px 24px; background: #1a73e8; color: white; border: 0; border-radius: 6px; font: inherit; cursor: pointer; }
        a { color: #1a73e8; }
    </style>
</head>
<body>
<main>
    <a href="{{ url('/') }}"><img src="{{ asset('images/logo_seomaster.svg') }}" width="180" alt="SEOmaster"></a>
    @if ($submitted)
        <h1>Danke für dein Feedback!</h1>
        <p>Deine Antwort ist angekommen. Sie hilft uns, SEOmaster zu verbessern.</p>
        <a href="{{ url('/') }}">Zur Startseite</a>
    @else
        <h1>Wie war deine Testphase?</h1>
        <p>Was hat gut funktioniert, was hat gefehlt? Dein freiwilliges Feedback dauert ungefähr eine Minute.</p>
        @if ($errors->any())
            <ul style="color:#b3261e;" role="alert">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        @endif
        <form method="POST" action="{{ $submitUrl }}">
            @csrf
            <fieldset>
                <legend>Wie zufrieden bist du mit SEOmaster? *</legend>
                <p id="rating-help">1 = sehr unzufrieden, 5 = sehr zufrieden</p>
                <div class="ratings" aria-describedby="rating-help">
                    @foreach (range(1, 5) as $rating)
                        <label><input type="radio" name="rating" value="{{ $rating }}" @checked(old('rating') == $rating) required> {{ $rating }}</label>
                    @endforeach
                </div>
            </fieldset>
            <label for="reason">Was beschreibt deinen nächsten Schritt am besten? *</label>
            <select id="reason" name="reason" required>
                <option value="">Bitte auswählen</option>
                @foreach ($reasons as $value => $label)
                    <option value="{{ $value }}" @selected(old('reason') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <label for="comment">Was können wir verbessern? (optional)</label>
            <textarea id="comment" name="comment" rows="5" maxlength="3000" placeholder="Welche Funktion fehlt dir oder wo bist du nicht weitergekommen?">{{ old('comment') }}</textarea>
            <p style="font-size:14px;">Deine Antwort wird deinem Testkonto zugeordnet und intern zur Verbesserung von SEOmaster ausgewertet. <a href="{{ route('legal.datenschutz') }}">Datenschutz</a></p>
            <button type="submit">Feedback senden</button>
        </form>
    @endif
</main>
</body>
</html>

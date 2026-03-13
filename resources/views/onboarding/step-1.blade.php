<x-layouts.onboarding :step="$step" :steps="$steps" :progress="$progress" :totalSteps="$totalSteps" title="Willkommen">

<div class="ob-step-badge">
    <span>✦</span> Schritt 1 von {{ $totalSteps }}
</div>

<h1 class="ob-title">
    Willkommen bei<br>
    <img src="{{ asset('images/logo_seomaster.svg') }}" width="180" alt="SEOmaster Logo" style="margin-bottom: 16px;">
</h1>

<p class="ob-subtitle">
    Lass uns dein Konto in wenigen Minuten einrichten.<br>
    Wie dürfen wir dich ansprechen?
</p>

<form method="POST" action="{{ route('onboarding.welcome.save') }}">
    @csrf

    <div class="form-group">
        <label class="form-label" for="name">DEIN NAME</label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-input {{ $errors->has('name') ? 'error' : '' }}"
            value="{{ old('name', $user->name) }}"
            placeholder="Max Muster"
            autocomplete="name"
            autofocus
        >
        @error('name')
            <div class="form-error">⚠ {{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="timezone">ZEITZONE</label>
        <select id="timezone" name="timezone" class="form-select">
            @php
                $timezones = [
                    'Häufig genutzt' => [
                        'Europe/Berlin'   => 'Europa/Berlin (UTC+1/+2)',
                        'Europe/Vienna'   => 'Europa/Wien (UTC+1/+2)',
                        'Europe/Zurich'   => 'Europa/Zürich (UTC+1/+2)',
                        'Europe/London'   => 'Europa/London (UTC+0/+1)',
                        'UTC'             => 'UTC',
                    ],
                    'Europa' => [
                        'Europe/Amsterdam' => 'Amsterdam',
                        'Europe/Brussels'  => 'Brüssel',
                        'Europe/Paris'     => 'Paris',
                        'Europe/Rome'      => 'Rom',
                        'Europe/Madrid'    => 'Madrid',
                        'Europe/Warsaw'    => 'Warschau',
                        'Europe/Prague'    => 'Prag',
                        'Europe/Budapest'  => 'Budapest',
                    ],
                ];
                $selected = old('timezone', $user->timezone ?? 'Europe/Berlin');
            @endphp

            @foreach($timezones as $group => $tzList)
                <optgroup label="{{ $group }}">
                    @foreach($tzList as $value => $label)
                        <option value="{{ $value }}" {{ $selected === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        @error('timezone')
            <div class="form-error">⚠ {{ $message }}</div>
        @enderror
    </div>

    {{-- Trial badge --}}
    @php $sub = $user->activeSubscription()->first(); @endphp
    @if($sub && $sub->isOnTrial())
        <div class="alert alert-info" style="margin-bottom: 24px;">
            🎉 <strong>3-Tage-Trial aktiv</strong> —
            endet am {{ $sub->trial_ends_at?->format('d.m.Y') }}.
            Kein Kreditkarte benötigt.
        </div>
    @endif

    <button type="submit" class="btn-next">
        Weiter → Shop verbinden
    </button>
</form>

</x-layouts.onboarding>

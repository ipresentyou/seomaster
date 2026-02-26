<x-layouts.guest title="Registrieren">

    <x-slot name="footer">
        Bereits registriert? <a href="{{ route('login') }}">Anmelden →</a>
    </x-slot>

    <div class="auth-header">
        <div class="auth-title">Account erstellen</div>
        <div class="auth-subtitle">Starte kostenlos — 14 Tage Trial inklusive, keine Kreditkarte nötig.</div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        {{-- Name --}}
        <div class="form-group">
            <label class="form-label" for="name">Vollständiger Name</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="form-input {{ $errors->has('name') ? 'error' : '' }}"
                placeholder="Max Mustermann"
                required
                autofocus
                autocomplete="name"
            >
            @error('name')
                <div class="form-error">⚠ {{ $message }}</div>
            @enderror
        </div>

        {{-- E-Mail --}}
        <div class="form-group">
            <label class="form-label" for="email">E-Mail-Adresse</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                placeholder="du@beispiel.de"
                required
                autocomplete="email"
            >
            @error('email')
                <div class="form-error">⚠ {{ $message }}</div>
            @enderror
        </div>

        {{-- Passwort --}}
        <div class="form-group">
            <label class="form-label" for="password">Passwort</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                placeholder="Mindestens 8 Zeichen"
                required
                autocomplete="new-password"
                oninput="checkStrength(this.value)"
            >
            <div class="strength-bar">
                <div class="strength-fill" id="strengthFill"></div>
            </div>
            <div class="form-hint" id="strengthText" style="margin-top:5px;"></div>
            @error('password')
                <div class="form-error">⚠ {{ $message }}</div>
            @enderror
        </div>

        {{-- Passwort bestätigen --}}
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Passwort bestätigen</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-input"
                placeholder="••••••••"
                required
                autocomplete="new-password"
            >
        </div>

        {{-- AGB --}}
        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" name="terms" required {{ old('terms') ? 'checked' : '' }}>
                Ich akzeptiere die <a href="/terms" target="_blank">AGB</a> und
                <a href="/privacy" target="_blank">Datenschutzerklärung</a>.
            </label>
            @error('terms')
                <div class="form-error" style="margin-top:6px;">⚠ {{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">
            Kostenlos starten →
        </button>

        <div style="text-align:center; margin-top:14px; font-size:11px; color:var(--text-3);">
            🔒 SSL-verschlüsselt · 14 Tage Trial · Jederzeit kündbar
        </div>
    </form>

    <script>
    function checkStrength(password) {
        const fill = document.getElementById('strengthFill');
        const text = document.getElementById('strengthText');
        let score = 0;
        if (password.length >= 8)  score++;
        if (password.length >= 12) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        const levels = [
            { pct: '0%',   color: '',                           label: '' },
            { pct: '20%',  color: '#f43f5e',                   label: 'Sehr schwach' },
            { pct: '40%',  color: '#f59e0b',                   label: 'Schwach' },
            { pct: '65%',  color: '#f59e0b',                   label: 'Mittel' },
            { pct: '85%',  color: '#10b981',                   label: 'Stark' },
            { pct: '100%', color: '#10b981',                   label: '✓ Sehr stark' },
        ];
        const l = levels[score] || levels[0];
        fill.style.width    = password.length ? l.pct : '0%';
        fill.style.background = l.color;
        text.textContent = l.label;
        text.style.color = l.color || 'var(--text-3)';
    }
    </script>

</x-layouts.guest>

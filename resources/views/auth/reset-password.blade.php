<x-layouts.guest title="Neues Passwort setzen">

    <x-slot name="footer">
        <a href="{{ route('login') }}">← Zurück zur Anmeldung</a>
    </x-slot>

    <div class="auth-header">
        <div class="auth-title">Neues Passwort</div>
        <div class="auth-subtitle">Wähle ein starkes, einzigartiges Passwort für deinen Account.</div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- E-Mail --}}
        <div class="form-group">
            <label class="form-label" for="email">E-Mail-Adresse</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                placeholder="du@beispiel.de"
                required
                autofocus
            >
            @error('email')
                <div class="form-error">⚠ {{ $message }}</div>
            @enderror
        </div>

        {{-- Neues Passwort --}}
        <div class="form-group">
            <label class="form-label" for="password">Neues Passwort</label>
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
            @error('password')
                <div class="form-error">⚠ {{ $message }}</div>
            @enderror
        </div>

        {{-- Bestätigung --}}
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

        <button type="submit" class="btn-submit">
            Passwort speichern →
        </button>
    </form>

    <script>
    function checkStrength(password) {
        const fill = document.getElementById('strengthFill');
        let score = 0;
        if (password.length >= 8)  score++;
        if (password.length >= 12) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        const colors = ['', '#f43f5e', '#f59e0b', '#f59e0b', '#10b981', '#10b981'];
        const pcts   = ['0%', '20%', '40%', '65%', '85%', '100%'];
        fill.style.width      = password.length ? pcts[score] : '0%';
        fill.style.background = colors[score] || '';
    }
    </script>

</x-layouts.guest>

<x-layouts.guest title="Anmelden">

    <x-slot name="footer">
        Noch kein Account? <a href="{{ route('register') }}">Jetzt registrieren →</a>
    </x-slot>

    <div class="auth-header">
        <div class="auth-title">Willkommen zurück</div>
        <div class="auth-subtitle">Melde dich an, um deine SEO-Projekte zu verwalten.</div>
    </div>

    {{-- Session Status --}}
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Error Summary --}}
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                autofocus
                autocomplete="email"
            >
            @error('email')
                <div class="form-error">⚠ {{ $message }}</div>
            @enderror
        </div>

        {{-- Passwort --}}
        <div class="form-group">
            <label class="form-label" for="password">
                Passwort
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Vergessen?</a>
                @endif
            </label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            >
            @error('password')
                <div class="form-error">⚠ {{ $message }}</div>
            @enderror
        </div>

        {{-- Eingeloggt bleiben --}}
        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Angemeldet bleiben
            </label>
        </div>

        <button type="submit" class="btn-submit">
            Anmelden →
        </button>
    </form>

</x-layouts.guest>

<x-layouts.guest title="Passwort zurücksetzen">

    <x-slot name="footer">
        <a href="{{ route('login') }}">← Zurück zur Anmeldung</a>
    </x-slot>

    <div class="auth-header">
        <div class="auth-title">Passwort vergessen?</div>
        <div class="auth-subtitle">
            Gib deine E-Mail-Adresse ein. Wir senden dir einen Link zum Zurücksetzen deines Passworts.
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">
            ✅ {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

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
            >
            @error('email')
                <div class="form-error">⚠ {{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">
            Reset-Link senden →
        </button>
    </form>

</x-layouts.guest>

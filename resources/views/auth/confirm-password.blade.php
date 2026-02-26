<x-layouts.guest title="Passwort bestätigen">

    <x-slot name="footer">
        <a href="{{ route('dashboard') }}">← Zurück zum Dashboard</a>
    </x-slot>

    <div class="auth-header">
        <div class="auth-title">Identität bestätigen</div>
        <div class="auth-subtitle">
            Für diese sensible Aktion musst du dein Passwort bestätigen.
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="password">Aktuelles Passwort</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                placeholder="••••••••"
                required
                autofocus
                autocomplete="current-password"
            >
            @error('password')
                <div class="form-error">⚠ {{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">
            Bestätigen →
        </button>
    </form>

</x-layouts.guest>

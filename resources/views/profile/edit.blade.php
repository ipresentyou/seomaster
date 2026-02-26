<x-layouts.app title="Profil">

    <div class="page-header">
        <div>
            <div class="page-title">👤 Mein Profil</div>
            <div class="page-subtitle">Name, E-Mail und Passwort verwalten</div>
        </div>
    </div>

    {{-- ── Profil-Info ─────────────────────────────────────────────── --}}
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <div>
                <div class="card-title">Profil-Informationen</div>
                <div class="card-subtitle">Name und E-Mail-Adresse aktualisieren</div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}" style="max-width: 480px;">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label class="form-label" for="name">Name</label>
                    <input
                        id="name" type="text" name="name"
                        value="{{ old('name', auth()->user()->name) }}"
                        class="form-input {{ $errors->updateProfile->has('name') ? 'error' : '' }}"
                        required autofocus autocomplete="name"
                    >
                    @if($errors->updateProfile->has('name'))
                        <div class="form-error">⚠ {{ $errors->updateProfile->first('name') }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">E-Mail</label>
                    <input
                        id="email" type="email" name="email"
                        value="{{ old('email', auth()->user()->email) }}"
                        class="form-input {{ $errors->updateProfile->has('email') ? 'error' : '' }}"
                        required autocomplete="email"
                    >
                    @if($errors->updateProfile->has('email'))
                        <div class="form-error">⚠ {{ $errors->updateProfile->first('email') }}</div>
                    @enderror

                    @if(auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                        <div style="margin-top: 8px; padding: 8px 12px; background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.25); border-radius: 8px; font-size: 12px; color: #fbbf24;">
                            ⚠️ E-Mail nicht verifiziert.
                            <form method="POST" action="{{ route('verification.send') }}" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:none;border:none;color:#a78bfa;cursor:pointer;font-size:12px;text-decoration:underline;padding:0;">
                                    Erneut senden
                                </button>
                            </form>
                        </div>
                        @if(session('status') === 'verification-link-sent')
                            <div class="form-hint" style="color: var(--success); margin-top:4px;">✅ Bestätigungsmail gesendet.</div>
                        @endif
                    @endif
                </div>

                <div style="display:flex; align-items:center; gap:12px;">
                    <button type="submit" class="btn btn-primary">Speichern</button>
                    @if(session('status') === 'profile-updated')
                        <span style="font-size:12px; color: var(--success);">✅ Gespeichert</span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── Passwort ändern ─────────────────────────────────────────── --}}
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <div>
                <div class="card-title">Passwort ändern</div>
                <div class="card-subtitle">Verwende ein starkes, einzigartiges Passwort</div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('password.update') }}" style="max-width: 480px;">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="current_password">Aktuelles Passwort</label>
                    <input
                        id="current_password" type="password" name="current_password"
                        class="form-input {{ $errors->updatePassword->has('current_password') ? 'error' : '' }}"
                        autocomplete="current-password"
                    >
                    @if($errors->updatePassword->has('current_password'))
                        <div class="form-error">⚠ {{ $errors->updatePassword->first('current_password') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">Neues Passwort</label>
                    <input
                        id="new_password" type="password" name="password"
                        class="form-input {{ $errors->updatePassword->has('password') ? 'error' : '' }}"
                        autocomplete="new-password"
                    >
                    @if($errors->updatePassword->has('password'))
                        <div class="form-error">⚠ {{ $errors->updatePassword->first('password') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Passwort bestätigen</label>
                    <input
                        id="password_confirmation" type="password" name="password_confirmation"
                        class="form-input"
                        autocomplete="new-password"
                    >
                </div>

                <div style="display:flex; align-items:center; gap:12px;">
                    <button type="submit" class="btn btn-primary">Passwort aktualisieren</button>
                    @if(session('status') === 'password-updated')
                        <span style="font-size:12px; color: var(--success);">✅ Passwort geändert</span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── Account löschen ─────────────────────────────────────────── --}}
    <div class="card" style="border-color: rgba(244,63,94,0.2);">
        <div class="card-header">
            <div>
                <div class="card-title" style="color: #fb7185;">Account löschen</div>
                <div class="card-subtitle">Alle Daten werden dauerhaft gelöscht</div>
            </div>
        </div>
        <div class="card-body">
            <p style="font-size:13px; color:var(--text-2); margin-bottom:16px; max-width:480px;">
                ⚠️ Diese Aktion kann nicht rückgängig gemacht werden. Alle Projekte, Credentials und Daten werden unwiderruflich gelöscht.
            </p>

            <button
                type="button"
                class="btn btn-danger"
                onclick="document.getElementById('deleteModal').style.display='flex'"
            >
                Account löschen
            </button>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" style="
        display:none; position:fixed; inset:0; z-index:200;
        background:rgba(0,0,0,0.7); backdrop-filter:blur(4px);
        align-items:center; justify-content:center; padding:24px;
    ">
        <div style="background:#0e0e1a; border:1px solid rgba(244,63,94,0.3); border-radius:14px; padding:32px; max-width:400px; width:100%;">
            <div style="font-size:24px; margin-bottom:12px;">⚠️</div>
            <div style="font-size:16px; font-weight:600; color:var(--text-1); margin-bottom:8px;">Account wirklich löschen?</div>
            <p style="font-size:13px; color:var(--text-2); margin-bottom:20px; line-height:1.6;">
                Gib dein Passwort ein, um die Löschung zu bestätigen. Alle Daten werden sofort entfernt.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')

                <div class="form-group">
                    <input
                        type="password" name="password"
                        class="form-input"
                        placeholder="Dein aktuelles Passwort"
                        required
                    >
                    @if($errors->userDeletion->has('password'))
                        <div class="form-error">⚠ {{ $errors->userDeletion->first('password') }}</div>
                    @endif
                </div>

                <div style="display:flex; gap:10px;">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="document.getElementById('deleteModal').style.display='none'"
                        style="flex:1;"
                    >
                        Abbrechen
                    </button>
                    <button type="submit" class="btn btn-danger" style="flex:1;">
                        Löschen
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>

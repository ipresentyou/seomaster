<x-layouts.guest title="E-Mail bestätigen">

    <div class="auth-header">
        <div class="auth-title">E-Mail bestätigen</div>
        <div class="auth-subtitle">
            Wir haben dir eine Bestätigungsmail geschickt. Klicke auf den Link darin, um deinen Account freizuschalten.
        </div>
    </div>

    {{-- Success after resend --}}
    @if(session('status') === 'verification-link-sent')
        <div class="alert alert-success">
            ✅ Ein neuer Bestätigungslink wurde an <strong>{{ auth()->user()->email }}</strong> gesendet.
        </div>
    @endif

    {{-- Visual indicator --}}
    <div style="
        display: flex; flex-direction: column; align-items: center;
        padding: 32px 0; gap: 12px;
        border: 1px solid rgba(124,58,237,0.15);
        border-radius: 12px; background: rgba(124,58,237,0.04);
        margin-bottom: 24px;
    ">
        <div style="font-size: 40px; filter: drop-shadow(0 0 16px rgba(124,58,237,0.5));">📬</div>
        <div style="font-size: 14px; font-weight: 500; color: var(--text-1);">Mail ist unterwegs</div>
        <div style="font-size: 12px; color: var(--text-3); text-align:center; max-width:260px; line-height:1.6;">
            Prüfe deinen Posteingang und ggf. den Spam-Ordner für
            <span style="color:var(--accent-light)">{{ auth()->user()->email }}</span>
        </div>
    </div>

    {{-- Resend --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-submit">
            Bestätigungsmail erneut senden
        </button>
    </form>

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}" style="margin-top: 12px;">
        @csrf
        <button type="submit" style="
            width: 100%; padding: 10px;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 9px;
            color: var(--text-2); font-size: 13px;
            cursor: pointer; transition: all 0.2s;
        " onmouseover="this.style.background='rgba(255,255,255,0.04)'"
           onmouseout="this.style.background='transparent'">
            Abmelden
        </button>
    </form>

</x-layouts.guest>

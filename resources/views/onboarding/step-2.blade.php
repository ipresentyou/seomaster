<x-layouts.onboarding :step="$step" :steps="$steps" :progress="$progress" :totalSteps="$totalSteps" title="Shop verbinden">

<div class="ob-step-badge">
    <span>🔌</span> Schritt 2 von {{ $totalSteps }}
</div>

<h1 class="ob-title">Shopware-Shop<br>verbinden</h1>

<p class="ob-subtitle">
    Verbinde deinen ersten Shop über die Shopware API.
    Du findest die Zugangsdaten unter
    <strong style="color:var(--text-1)">Einstellungen → System → Integrationen</strong>.
</p>

{{-- How-to hint box --}}
<div style="
    background: rgba(124,58,237,0.06);
    border: 1px solid rgba(124,58,237,0.15);
    border-radius: 12px;
    padding: 16px 18px;
    margin-bottom: 28px;
    font-size: 13px;
    line-height: 1.7;
    color: var(--text-2);
">
    <div style="font-weight:600; color:var(--accent-l); margin-bottom:8px;">📋 Wie bekomme ich die API-Daten?</div>
    <div>1. Shopware Admin → <em>Einstellungen → System → Integrationen</em></div>
    <div>2. Neue Integration anlegen → Berechtigungen: <em>Katalog lesen + schreiben</em></div>
    <div>3. <em>Client ID</em> und <em>Client Secret</em> kopieren</div>
</div>

<form method="POST" action="{{ route('onboarding.connect.save') }}" id="connect-form">
    @csrf

    <div class="form-group">
        <label class="form-label" for="shop_url">SHOP-URL</label>
        <input
            type="url"
            id="shop_url"
            name="shop_url"
            class="form-input {{ $errors->has('shop_url') ? 'error' : '' }}"
            value="{{ old('shop_url') }}"
            placeholder="https://mein-shop.de"
            autocomplete="off"
        >
        @error('shop_url')
            <div class="form-error">⚠ {{ $message }}</div>
        @enderror
        <div class="form-hint">Die URL deines Shopware-Shops, ohne /api am Ende.</div>
    </div>

    <div class="form-group">
        <label class="form-label" for="client_id">CLIENT ID</label>
        <input
            type="text"
            id="client_id"
            name="client_id"
            class="form-input {{ $errors->has('client_id') ? 'error' : '' }}"
            value="{{ old('client_id') }}"
            placeholder="SWIAXXXXXXXXXXXXXXXXXXXXXXXXXX"
            autocomplete="off"
        >
        @error('client_id')
            <div class="form-error">⚠ {{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="client_secret">CLIENT SECRET</label>
        <div style="position:relative;">
            <input
                type="password"
                id="client_secret"
                name="client_secret"
                class="form-input {{ $errors->has('client_secret') ? 'error' : '' }}"
                value="{{ old('client_secret') }}"
                placeholder="••••••••••••••••••••••••••••••••"
                autocomplete="new-password"
                style="padding-right: 44px;"
            >
            <button type="button"
                onclick="toggleSecret()"
                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);
                       background:none;border:none;cursor:pointer;color:var(--text-3);font-size:16px;"
                id="secret-toggle" title="Anzeigen/Verbergen">
                👁
            </button>
        </div>
        @error('client_secret')
            <div class="form-error">⚠ {{ $message }}</div>
        @enderror
        <div class="form-hint">
            🔒 Das Secret wird verschlüsselt gespeichert und nie im Klartext angezeigt.
        </div>
    </div>

    {{-- Test indicator --}}
    <div id="test-indicator" style="display:none;
        padding:10px 14px; border-radius:8px; font-size:13px;
        margin-bottom:16px; border: 1px solid transparent;">
    </div>

    <button type="submit" class="btn-next" id="submit-btn">
        🔌 Verbinden & weiter
    </button>

    {{-- Skip --}}
    <button type="submit" name="skip" value="1" class="btn-skip-step">
        Jetzt überspringen — später in Einstellungen hinzufügen
    </button>

</form>

<script>
function toggleSecret() {
    const field  = document.getElementById('client_secret');
    const toggle = document.getElementById('secret-toggle');
    if (field.type === 'password') {
        field.type = 'text';
        toggle.textContent = '🙈';
    } else {
        field.type = 'password';
        toggle.textContent = '👁';
    }
}

// Live connection test on blur
document.getElementById('client_secret').addEventListener('blur', async function () {
    const url    = document.getElementById('shop_url').value.trim();
    const id     = document.getElementById('client_id').value.trim();
    const secret = this.value.trim();
    const ind    = document.getElementById('test-indicator');

    if (!url || !id || !secret) return;

    ind.style.display = 'block';
    ind.style.background   = 'rgba(124,58,237,0.06)';
    ind.style.borderColor  = 'rgba(124,58,237,0.2)';
    ind.style.color        = 'var(--accent-l)';
    ind.textContent        = '⏳ Verbindung wird getestet...';

    try {
        const res = await fetch('{{ route("onboarding.connect.test") }}', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({ shop_url: url, client_id: id, client_secret: secret })
        });
        const data = await res.json();

        if (data.success) {
            ind.style.background  = 'rgba(16,185,129,0.08)';
            ind.style.borderColor = 'rgba(16,185,129,0.3)';
            ind.style.color       = '#34d399';
            ind.textContent       = '✅ Verbindung erfolgreich! Shop-Version: ' + (data.version ?? '–');
        } else {
            ind.style.background  = 'rgba(244,63,94,0.08)';
            ind.style.borderColor = 'rgba(244,63,94,0.3)';
            ind.style.color       = '#fb7185';
            ind.textContent       = '❌ ' + (data.message ?? 'Verbindung fehlgeschlagen');
        }
    } catch (e) {
        ind.style.background  = 'rgba(245,158,11,0.08)';
        ind.style.borderColor = 'rgba(245,158,11,0.3)';
        ind.style.color       = '#fbbf24';
        ind.textContent       = '⚠ Test nicht möglich (CORS/Netzwerk)';
    }
});
</script>

</x-layouts.onboarding>

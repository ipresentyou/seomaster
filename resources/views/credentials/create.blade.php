<x-layouts.app title="Credentials hinzufügen">

<style>
.create-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 24px;
    align-items: start;
}

/* Provider Selector */
.provider-selector { display: flex; flex-direction: column; gap: 6px; }

.provider-option {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 14px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.02);
    cursor: pointer;
    transition: all 0.15s;
    user-select: none;
}
.provider-option:hover { border-color: rgba(124,58,237,0.3); background: rgba(124,58,237,0.05); }
.provider-option.selected {
    border-color: rgba(124,58,237,0.5);
    background: rgba(124,58,237,0.12);
}

.provider-option-icon {
    width: 34px; height: 34px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; flex-shrink: 0;
}

.provider-option-text { flex: 1; }
.provider-option-name { font-size: 13px; font-weight: 500; }
.provider-option-desc { font-size: 11px; color: var(--text-3); margin-top: 1px; }

.check-mark {
    width: 18px; height: 18px; border-radius: 50%;
    background: var(--accent);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px;
    opacity: 0;
    transition: opacity 0.15s;
}
.provider-option.selected .check-mark { opacity: 1; }

/* Form card */
.form-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 12px;
    padding: 24px;
}

.form-section-title {
    font-size: 13px; font-weight: 600;
    color: var(--text-2);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

/* Field group with icon */
.field-with-icon { position: relative; }
.field-with-icon .form-input { padding-left: 38px; }
.field-icon {
    position: absolute; left: 12px; top: 50%;
    transform: translateY(-50%);
    font-size: 14px; color: var(--text-3);
    pointer-events: none;
}

/* Password toggle */
.password-wrapper { position: relative; }
.password-toggle {
    position: absolute; right: 10px; top: 50%;
    transform: translateY(-50%);
    background: none; border: none;
    color: var(--text-3); cursor: pointer; font-size: 14px;
    padding: 4px;
}
.password-toggle:hover { color: var(--text-1); }

/* Help link */
.help-link { font-size: 11px; color: var(--accent-light); text-decoration: none; }
.help-link:hover { text-decoration: underline; }

/* Provider-specific fields: hide all, show selected */
.provider-fields { display: none; }
.provider-fields.visible { display: block; }
</style>

<div class="page-header">
    <div>
        <div class="page-title">🔑 Credentials hinzufügen</div>
        <div class="page-subtitle">Wähle einen Dienst und gib deine API-Keys ein</div>
    </div>
    <a href="{{ route('credentials.index') }}" class="btn btn-secondary">← Zurück</a>
</div>

<form method="POST" action="{{ route('credentials.store') }}" id="credForm">
@csrf

<!-- Hidden provider field to ensure provider is always submitted -->
<input type="hidden" name="provider" id="selected-provider" value="{{ $preselected ?? 'shopware' }}">

<div class="create-grid">

    {{-- ── Provider Selector ────────────────────────────────── --}}
    <div>
        <div style="font-size:12px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px;">
            Dienst wählen
        </div>
        <div class="provider-selector">
            @php
                $providers = [
                    'shopware'              => ['icon'=>'🛒','bg'=>'rgba(14,165,233,0.12)','name'=>'Shopware 6','desc'=>'API-Zugang für deinen Shop'],
                    'openai'                => ['icon'=>'🤖','bg'=>'rgba(16,185,129,0.12)','name'=>'OpenAI','desc'=>'GPT-4o für SEO-Texte & Alt-Text'],
                    'gemini'                => ['icon'=>'✨','bg'=>'rgba(245,158,11,0.12)','name'=>'Google Gemini','desc'=>'Gemini Pro als Alternative'],
                    'google_search_console' => ['icon'=>'📊','bg'=>'rgba(234,88,12,0.12)', 'name'=>'Google Search Console','desc'=>'Ranking & Performance-Daten'],
                ];
                $preselected = request('provider', 'shopware');
            @endphp

            @foreach($providers as $key => $p)
                <label class="provider-option {{ $key === $preselected ? 'selected' : '' }}"
                       onclick="selectProvider('{{ $key }}')">
                    <input type="radio" name="provider" value="{{ $key }}"
                           {{ $key === $preselected ? 'checked' : '' }}
                           style="display:none;">
                    <div class="provider-option-icon" style="background:{{ $p['bg'] }}">{{ $p['icon'] }}</div>
                    <div class="provider-option-text">
                        <div class="provider-option-name">{{ $p['name'] }}</div>
                        <div class="provider-option-desc">{{ $p['desc'] }}</div>
                    </div>
                    <div class="check-mark">✓</div>
                </label>
            @endforeach
        </div>
    </div>

    {{-- ── Dynamic Form ─────────────────────────────────────── --}}
    <div class="form-card">

        {{-- Label (immer sichtbar) --}}
        <div class="form-group">
            <label class="form-label">Label <span style="color:var(--text-3)">(optional)</span></label>
            <div class="field-with-icon">
                <span class="field-icon">🏷️</span>
                <input type="text" name="label" class="form-input"
                       placeholder='z.B. "Shop DE", "Hauptshop"'
                       value="{{ old('label') }}">
            </div>
            <div class="form-hint">Hilft dir, mehrere Accounts desselben Dienstes zu unterscheiden.</div>
        </div>

        {{-- ── Shopware ────────────────────────────────────── --}}
        <div class="provider-fields {{ $preselected === 'shopware' ? 'visible' : '' }}" id="fields-shopware">
            <div class="form-section-title">🛒 Shopware 6 API</div>

            <div class="form-group">
                <label class="form-label">Shop URL</label>
                <div class="field-with-icon">
                    <span class="field-icon">🌐</span>
                    <input type="url" name="credentials[shop_url]" class="form-input"
                           placeholder="https://dein-shop.de"
                           value="{{ old('credentials.shop_url') }}">
                </div>
                <div class="form-hint">Basis-URL deines Shopware-Shops (ohne /api am Ende)</div>
            </div>

            <div class="form-group">
                <label class="form-label">Client ID</label>
                <div class="field-with-icon">
                    <span class="field-icon">🪪</span>
                    <input type="text" name="credentials[client_id]" class="form-input"
                           placeholder="SWIAXXXXXXXXXXXXXXXX"
                           value="{{ old('credentials.client_id') }}" autocomplete="off">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Client Secret</label>
                <div class="password-wrapper">
                    <input type="password" name="credentials[client_secret]" id="sw-secret" class="form-input"
                           placeholder="••••••••••••••••••••••••"
                           autocomplete="off" style="padding-right:40px;">
                    <button type="button" class="password-toggle" onclick="togglePw('sw-secret', this)">👁</button>
                </div>
                <div class="form-hint">
                    Zu finden unter: Shopware Admin → Einstellungen → System → Integrationen →
                    <a href="#" class="help-link">Neue Integration erstellen</a>
                </div>
            </div>
        </div>

        {{-- ── OpenAI ───────────────────────────────────────── --}}
        <div class="provider-fields {{ $preselected === 'openai' ? 'visible' : '' }}" id="fields-openai">
            <div class="form-section-title">🤖 OpenAI API</div>

            <div class="form-group">
                <label class="form-label">API Key</label>
                <div class="password-wrapper">
                    <input type="password" name="credentials[api_key]" id="oai-key" class="form-input"
                           placeholder="sk-proj-••••••••••••••••••"
                           autocomplete="off" style="padding-right:40px;">
                    <button type="button" class="password-toggle" onclick="togglePw('oai-key', this)">👁</button>
                </div>
                <div class="form-hint">
                    <a href="https://platform.openai.com/api-keys" target="_blank" class="help-link">
                        → API Key bei OpenAI erstellen
                    </a>
                    · Modelle: GPT-4o (Vision für Alt-Text), GPT-3.5-turbo (Meta/Text)
                </div>
            </div>

            <div style="background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.2); border-radius:8px; padding:12px 14px; font-size:12px; color:var(--text-2);">
                💡 <strong style="color:#34d399">Empfohlen</strong> für Alt-Text-Generierung (Bildanalyse via GPT-4o Vision) und SEO-Texte.
            </div>
        </div>

        {{-- ── Gemini ───────────────────────────────────────── --}}
        <div class="provider-fields {{ $preselected === 'gemini' ? 'visible' : '' }}" id="fields-gemini">
            <div class="form-section-title">✨ Google Gemini</div>

            <div class="form-group">
                <label class="form-label">API Key</label>
                <div class="password-wrapper">
                    <input type="password" name="credentials[api_key]" id="gem-key" class="form-input"
                           placeholder="AIzaSy••••••••••••••••••••••"
                           autocomplete="off" style="padding-right:40px;">
                    <button type="button" class="password-toggle" onclick="togglePw('gem-key', this)">👁</button>
                </div>
                <div class="form-hint">
                    <a href="https://aistudio.google.com/app/apikey" target="_blank" class="help-link">
                        → API Key bei Google AI Studio erstellen
                    </a>
                </div>
            </div>
        </div>

        {{-- ── GSC ──────────────────────────────────────────── --}}
        <div class="provider-fields {{ $preselected === 'google_search_console' ? 'visible' : '' }}" id="fields-google_search_console">
            <div class="form-section-title">📊 Google Search Console</div>

            <div class="alert alert-info" style="margin-bottom:16px;">
                GSC benötigt OAuth2. Du brauchst eine Google Cloud Console App mit aktivierter
                Search Console API.
                <a href="https://console.cloud.google.com" target="_blank" class="help-link">→ Google Cloud Console</a>
            </div>

            <div class="form-group">
                <label class="form-label">OAuth Client ID</label>
                <input type="text" name="credentials[client_id]" class="form-input"
                       placeholder="XXXXXX.apps.googleusercontent.com"
                       value="{{ old('credentials.client_id') }}">
            </div>

            <div class="form-group">
                <label class="form-label">OAuth Client Secret</label>
                <div class="password-wrapper">
                    <input type="password" name="credentials[client_secret]" id="gsc-secret" class="form-input"
                           placeholder="GOCSPX-••••••••••••••••"
                           autocomplete="off" style="padding-right:40px;">
                    <button type="button" class="password-toggle" onclick="togglePw('gsc-secret', this)">👁</button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Refresh Token</label>
                <div class="password-wrapper">
                    <input type="password" name="credentials[refresh_token]" id="gsc-rt" class="form-input"
                           placeholder="1//XXXXXXXX…"
                           autocomplete="off" style="padding-right:40px;">
                    <button type="button" class="password-toggle" onclick="togglePw('gsc-rt', this)">👁</button>
                </div>
                <div class="form-hint">Refresh Token aus OAuth2 Flow. Einmalig über Google OAuth Playground generieren.</div>
            </div>
        </div>

        {{-- Submit --}}
        <div style="display:flex; gap:10px; margin-top:24px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.05);">
            <button type="submit" class="btn btn-primary" style="flex:1;">
                🔒 Verschlüsselt speichern
            </button>
            <a href="{{ route('credentials.index') }}" class="btn btn-secondary">Abbrechen</a>
        </div>
    </div>

</div>
</form>

@push('scripts')
<script>
function selectProvider(key) {
    // Radio update
    document.querySelectorAll('input[name="provider"]').forEach(r => r.checked = r.value === key);

    // UI update
    document.querySelectorAll('.provider-option').forEach(el => {
        el.classList.toggle('selected', el.querySelector('input').value === key);
    });

    // Fields toggle
    document.querySelectorAll('.provider-fields').forEach(el => el.classList.remove('visible'));
    const fields = document.getElementById('fields-' + key);
    if (fields) fields.classList.add('visible');
}

function togglePw(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
    } else {
        input.type = 'password';
        btn.textContent = '👁';
    }
}
</script>
@endpush

</x-layouts.app>
<script>
// Override: Vor Submit alle nicht-aktiven Provider-Felder disablen
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('form').addEventListener('submit', function(e) {
        document.querySelectorAll('.provider-fields:not(.visible) input, .provider-fields:not(.visible) textarea').forEach(function(el) {
            el.disabled = true;
        });
    });
});

// Update hidden provider field when provider changes
function selectProvider(key) {
    // Update hidden field
    document.getElementById('selected-provider').value = key;
    
    // Update UI
    document.querySelectorAll('.provider-option').forEach(el => el.classList.remove('selected'));
    const option = document.querySelector('[data-provider="' + key + '"]');
    if (option) option.classList.add('selected');

    // Fields toggle
    document.querySelectorAll('.provider-fields').forEach(el => el.classList.remove('visible'));
    const fields = document.getElementById('fields-' + key);
    if (fields) fields.classList.add('visible');
}
</script>

<x-layouts.app title="API Credentials">

<style>
.provider-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}

.provider-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 12px;
    overflow: hidden;
    transition: border-color 0.2s, transform 0.2s;
}
.provider-card:hover { border-color: rgba(124,58,237,0.35); transform: translateY(-2px); }

.provider-card-header {
    padding: 16px 18px;
    display: flex; align-items: center; gap: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.provider-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.icon-shopware { background: rgba(14,165,233,0.12); }
.icon-openai   { background: rgba(16,185,129,0.12); }
.icon-gemini   { background: rgba(245,158,11,0.12); }
.icon-gsc      { background: rgba(234,88,12,0.12);  }

.provider-meta { flex: 1; min-width: 0; }
.provider-name { font-size: 14px; }
.provider-desc { font-size: 11px; color: var(--text-3); margin-top: 1px; }

.credential-list { padding: 0; }

.credential-item {
    display: flex; align-items: center;
    padding: 12px 18px;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    gap: 10px;
}
.credential-item:last-child { border-bottom: none; }

.cred-label { font-size: 13px; font-weight: 500; flex: 1; min-width: 0; }
.cred-label small { display: block; font-size: 11px; color: var(--text-3); font-weight: 400; }

.status-dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
}
.status-ok      { background: var(--success); box-shadow: 0 0 6px var(--success); }
.status-fail    { background: var(--danger); }
.status-unknown { background: var(--text-3); }

.cred-actions { display: flex; gap: 6px; }

.empty-provider {
    padding: 18px;
    text-align: center;
    color: var(--text-3);
    font-size: 12px;
}

.add-btn {
    display: flex; align-items: center; gap: 6px;
    padding: 8px 14px;
    margin: 12px 18px;
    border-radius: 8px;
    background: rgba(124,58,237,0.12);
    border: 1px solid rgba(124,58,237,0.4);
    color: #8b5cf6;
    font-size: 12px;
    text-decoration: none;
    transition: all 0.15s;
}
.add-btn:hover { 
    background: rgba(124,58,237,0.2); 
    border-color: rgba(124,58,237,0.6);
    color: #7c3aed;
    transform: translateY(-1px);
}

/* Test button */
.btn-test {
    padding: 4px 10px;
    border-radius: 6px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    color: var(--text-2);
    font-size: 11px; cursor: pointer;
    transition: all 0.15s;
}
.btn-test:hover { background: rgba(255,255,255,0.08); color: var(--text-1); }
.btn-test.testing { color: var(--warning); border-color: rgba(245,158,11,0.3); }
.btn-test.ok      { color: var(--success); border-color: rgba(16,185,129,0.3); }
.btn-test.fail    { color: var(--danger);  border-color: rgba(244,63,94,0.3); }

/* Encryption info box */
.encrypt-box {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 14px 16px;
    background: rgba(124,58,237,0.06);
    border: 1px solid rgba(124,58,237,0.2);
    border-radius: 10px;
    margin-bottom: 24px;
    font-size: 12px; color: var(--text-2);
    line-height: 1.6;
}
</style>

<div class="page-header">
    <div>
        <div class="page-title">🔑 API Credentials</div>
        <div class="page-subtitle">Verwalte deine API-Keys für alle verbundenen Dienste</div>
    </div>
    <a href="{{ route('credentials.create') }}" class="btn btn-primary">
        + Neue Credentials
    </a>
</div>

{{-- Encryption Notice --}}
<div class="encrypt-box">
    <span style="font-size:18px; margin-top:1px;">🔒</span>
    <div>
        <strong style="color:var(--text-1)">Ende-zu-Ende verschlüsselt</strong><br>
        Alle API-Keys werden mit AES-256-CBC verschlüsselt in der Datenbank gespeichert.
        Nur du kannst deine Keys lesen. SEOmaster hat zu keinem Zeitpunkt Zugang zu deinen Klartextdaten.
    </div>
</div>

@php
    $providers = [
        'shopware'               => ['name' => 'Shopware',              'icon' => '🛒', 'class' => 'icon-shopware', 'desc' => 'Shopware 6 API · Store-Verbindung'],
        'openai'                 => ['name' => 'OpenAI',                'icon' => '🤖', 'class' => 'icon-openai',   'desc' => 'GPT-4o · Texte & Alt-Texte'],
        'gemini'                 => ['name' => 'Google Gemini',         'icon' => '✨', 'class' => 'icon-gemini',   'desc' => 'Gemini Pro · Alternative KI'],
        'google_search_console'  => ['name' => 'Google Search Console', 'icon' => '📊', 'class' => 'icon-gsc',      'desc' => 'GSC API · Ranking & Clicks'],
    ];
@endphp

<div class="provider-grid">
    @foreach($providers as $providerKey => $provider)
        @php $creds = $credentials->get($providerKey, collect()); @endphp
        <div class="provider-card">
            <div class="provider-card-header">
                <div class="provider-icon {{ $provider['class'] }}">{{ $provider['icon'] }}</div>
                <div class="provider-meta">
                    <div class="provider-name">{{ $provider['name'] }}</div>
                    <div class="provider-desc">{{ $provider['desc'] }}</div>
                </div>
                @if($creds->isNotEmpty())
                    <span class="badge badge-green">{{ $creds->count() }} aktiv</span>
                @else
                    <span class="badge badge-gray">Nicht verbunden</span>
                @endif
            </div>

            @if($creds->isEmpty())
                <div class="empty-provider">Noch keine Verbindung eingerichtet</div>
            @else
                <div class="credential-list">
                    @foreach($creds as $cred)
                        <div class="credential-item" id="cred-row-{{ $cred->id }}">
                            <div class="status-dot {{ $cred->last_test_ok === true ? 'status-ok' : ($cred->last_test_ok === false ? 'status-fail' : 'status-unknown') }}"
                                 id="status-dot-{{ $cred->id }}"
                                 title="{{ $cred->last_tested_at ? 'Getestet: ' . $cred->last_tested_at->diffForHumans() : 'Noch nicht getestet' }}">
                            </div>
                            <div class="cred-label">
                                {{ $cred->label ?: $provider['name'] }}
                                <small>
                                    @if($cred->last_tested_at)
                                        Getestet {{ $cred->last_tested_at->diffForHumans() }}
                                    @else
                                        Nie getestet
                                    @endif
                                </small>
                            </div>
                            <div class="cred-actions">
                                <button class="btn-test"
                                        id="test-btn-{{ $cred->id }}"
                                        onclick="testCredential({{ $cred->id }})">
                                    ⚡ Test
                                </button>
                                <form method="POST"
                                      action="{{ route('credentials.destroy', $cred) }}"
                                      onsubmit="return confirm('Credentials wirklich löschen?')"
                                      style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-test" style="color:var(--danger); border-color:rgba(244,63,94,0.3);">
                                        🗑
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <a href="{{ route('credentials.create', ['provider' => $providerKey]) }}" class="add-btn">
                + {{ $providerKey === 'shopware' ? 'Shop hinzufügen' : 'Verbinden' }}
            </a>
        </div>
    @endforeach
</div>

@push('scripts')
<script>
async function testCredential(id) {
    const btn = document.getElementById('test-btn-' + id);
    const dot = document.getElementById('status-dot-' + id);

    btn.className = 'btn-test testing';
    btn.textContent = '⏳ Test…';

    const res = await fetch(`/credentials/${id}/test`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    });

    const data = await res.json();

    if (data.ok) {
        btn.className = 'btn-test ok';
        btn.textContent = '✅ OK';
        dot.className = 'status-dot status-ok';
    } else {
        btn.className = 'btn-test fail';
        btn.textContent = '❌ Fehler';
        dot.className = 'status-dot status-fail';
    }

    setTimeout(() => {
        btn.className = 'btn-test';
        btn.textContent = '⚡ Test';
    }, 3000);
}
</script>
@endpush

</x-layouts.app>

<x-layouts.app title="Alt-Text Editor – {{ $project->name }}">

@push('styles')
<style>
.seo-toolbar {
    display:flex;align-items:center;gap:10px;padding:14px 0;margin-bottom:20px;
    border-bottom:1px solid rgba(255,255,255,0.05);flex-wrap:wrap;
}
.seo-select,.seo-input {
    height:36px;padding:0 10px;background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,255,255,0.1);border-radius:8px;
    color:var(--text-1);font-size:13px;font-family:inherit;outline:none;
}
.seo-input { width:80px; }
.seo-select:focus,.seo-input:focus { border-color:var(--accent); }
.toolbar-right { margin-left:auto;display:flex;gap:8px; }

.stats-bar {
    display:flex;gap:16px;padding:10px 14px;background:rgba(255,255,255,0.02);
    border:1px solid rgba(255,255,255,0.06);border-radius:8px;font-size:12px;
    color:var(--text-2);margin-bottom:22px;flex-wrap:wrap;
}
.stats-bar strong { color:var(--text-1); }

.encrypt-notice {
    display:flex;align-items:flex-start;gap:10px;padding:12px 14px;
    background:rgba(124,58,237,0.06);border:1px solid rgba(124,58,237,0.2);
    border-radius:8px;margin-bottom:20px;font-size:12px;color:var(--text-2);line-height:1.6;
}

/* Image grid */
.img-grid {
    display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:16px;
}

.img-card {
    background:var(--card-bg);border:1px solid var(--card-border);border-radius:12px;overflow:hidden;
    transition:border-color 0.2s;
}
.img-card:hover { border-color:rgba(124,58,237,0.35); }

.img-preview {
    width:100%;height:180px;background:rgba(255,255,255,0.03);
    display:flex;align-items:center;justify-content:center;overflow:hidden;
    border-bottom:1px solid rgba(255,255,255,0.05);
}
.img-preview img { max-width:100%;max-height:100%;object-fit:contain; }

.img-body { padding:14px; }
.img-filename { font-size:13px;font-weight:500;margin-bottom:4px;word-break:break-all; }
.img-alt {
    font-size:12px;color:var(--text-2);margin-bottom:8px;line-height:1.5;
    font-style:italic;
}
.img-alt.missing { color:var(--danger); }
.img-meta { display:flex;gap:8px;font-size:11px;color:var(--text-3);flex-wrap:wrap;margin-bottom:10px; }
.img-meta span { display:flex;align-items:center;gap:3px; }

.img-actions { display:flex;gap:7px; }
.img-actions .btn { padding:5px 11px;font-size:12px; }

/* Edit area inline */
.img-edit { padding:12px 14px;border-top:1px solid rgba(255,255,255,0.05);display:none; }
.ai-note {
    display:flex;align-items:center;gap:7px;padding:7px 10px;border-radius:6px;
    background:rgba(124,58,237,0.15);border:1px solid rgba(124,58,237,0.4);
    font-size:11px;color:#a78bfa;font-weight:500;margin-bottom:10px;
}

.ep-input {
    width:100%;padding:8px 11px;background:#fff;
    border:1px solid #ccc;border-radius:8px;
    color:#000;font-family:inherit;font-size:13px;outline:none;
    transition:border-color 0.2s;
}
.ep-input:focus { border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,0.2); }
.char-info { font-size:11px;margin-top:4px;display:flex;justify-content:space-between; }
.char-info.good { color:var(--success); }
.char-info.warn { color:var(--warning); }
.char-info.over { color:var(--danger); }

/* Progress overlay for batch */
.batch-overlay {
    position:fixed;bottom:24px;right:24px;
    background:var(--card-bg);border:1px solid rgba(124,58,237,0.4);
    border-radius:10px;padding:14px 18px;
    font-size:13px;z-index:999;min-width:220px;
    box-shadow:0 8px 32px rgba(0,0,0,0.4);
}
.batch-progress {
    height:4px;background:rgba(255,255,255,0.08);border-radius:99px;margin-top:8px;overflow:hidden;
}
.batch-progress-fill {
    height:100%;background:linear-gradient(90deg,var(--accent),#9333ea);
    border-radius:99px;transition:width 0.3s;
}
</style>
@endpush

<div class="page-header">
    <div>
        <div class="page-title">🖼️ Alt-Text Editor</div>
        <div class="page-subtitle">{{ $project->name }} · Bilder für Suchmaschinen optimieren</div>
    </div>
</div>

{{-- Toolbar --}}
<form method="GET" id="filterForm">
    <div class="seo-toolbar">
        <select class="seo-select" name="sc" onchange="this.form.submit()">
            @foreach($salesChannels as $id => $sc)
                <option value="{{ $id }}" {{ $id === $selectedSc ? 'selected' : '' }}>{{ $sc['name'] }}</option>
            @endforeach
        </select>
        <select class="seo-select" name="lang" onchange="this.form.submit()">
            @foreach($domains[$selectedSc] ?? [] as $lId => $d)
                <option value="{{ $lId }}" {{ $lId === $selectedLang ? 'selected' : '' }}>
                    {{ $languages[$lId] ?? $lId }}
                </option>
            @endforeach
        </select>
        <select class="seo-select" name="filter">
            <option value="missing" {{ $filterType === 'missing' ? 'selected' : '' }}>❌ Nur fehlende Alt-Texte</option>
            <option value="all"     {{ $filterType === 'all'     ? 'selected' : '' }}>📸 Alle Bilder</option>
        </select>
        <input type="number" class="seo-input" name="max" value="{{ $limit }}">
        <button type="submit" class="btn btn-primary" style="padding:7px 14px;font-size:13px;">Laden</button>
        <div class="toolbar-right">
            <button type="button" class="btn btn-secondary" style="padding:7px 14px;font-size:13px;" onclick="generateAll()">
                ✨ Alle generieren
            </button>
            <button type="button" class="btn btn-primary" style="padding:7px 14px;font-size:13px;" onclick="batchSave()" id="batch-save-btn" disabled>
                💾 Alle speichern (<span id="pending-count">0</span>)
            </button>
        </div>
    </div>
</form>

<div class="encrypt-notice">
    <span style="font-size:16px">🔒</span>
    <div>
        <strong style="color:var(--text-1)">KI-Bildanalyse via GPT-4o Vision</strong><br>
        Jedes Bild wird einzeln durch die OpenAI Vision API analysiert.
        Deine API-Keys sind verschlüsselt gespeichert und werden sicher übermittelt.
    </div>
</div>

<details style="margin-bottom:20px;">
    <summary style="font-size:12px;color:var(--text-3);cursor:pointer;padding:6px 0;">⚙️ KI-Anweisungen anpassen</summary>
    <textarea id="customPrompt" rows="3" class="form-input" style="margin-top:8px;font-size:12px;resize:vertical;"
>Erstelle präzise, SEO-optimierte Alt-Texte (50–125 Zeichen) für Produkte aus "' . ($project->name ?? 'diesem Shop') . '". Beschreibe was sichtbar ist, integriere relevante Keywords natürlich. Kein "Bild von" oder "Zeigt". Sprache: {{ $languages[$selectedLang] ?? 'Deutsch' }}.</textarea>
    <div style="display:flex;gap:8px;margin-top:6px;align-items:center;">
        <button onclick="savePrompt()" class="ep-btn ep-btn-primary" style="font-size:12px;padding:4px 12px;">💾 Speichern</button>
        <button onclick="resetPrompt()" class="ep-btn ep-btn-secondary" style="font-size:12px;padding:4px 12px;">🔄 Zurücksetzen</button>
        <span id="prompt-status" style="font-size:11px;color:var(--text-3);"></span>
    </div>
</details>

@isset($connectionError)
<div style="text-align:center;padding:60px;color:var(--text-3);">
    <div style="font-size:40px;margin-bottom:12px;">🔌</div>
    <div style="font-size:15px;color:var(--text-2);margin-bottom:8px;">Verbindungsproblem</div>
    <div style="font-size:13px;line-height:1.5;">{{ $connectionError }}</div>
    <div style="margin-top:16px;">
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary">
            ⚙️ Projekteinstellungen überprüfen
        </a>
    </div>
</div>
@else
@if($rows)
<div class="stats-bar">
    <span>Bilder gesamt: <strong>{{ count($rows) }}</strong></span>
    <span>Fehlende Alt-Texte: <strong style="color:{{ $missingCount > 0 ? 'var(--danger)' : 'var(--success)' }}">{{ $missingCount }}</strong></span>
    <span>Gesamtgröße: <strong>{{ round($totalSize / 1024 / 1024, 2) }} MB</strong></span>
    <span>Sprache: <strong>{{ $languages[$selectedLang] ?? $selectedLang }}</strong></span>
</div>
@endif

{{-- Image Grid --}}
@if(empty($rows))
    <div style="text-align:center;padding:60px;color:var(--text-3);">
        <div style="font-size:40px;margin-bottom:12px;">🖼️</div>
        <div style="font-size:15px;color:var(--text-2);">Keine Bilder gefunden</div>
        <div style="font-size:13px;margin-top:6px;">Versuche den Filter "Alle Bilder" oder erhöhe das Limit.</div>
    </div>
@else
<div class="img-grid">
    @foreach($rows as $idx => $img)
    <div class="img-card" id="card-{{ $idx }}">
        <div class="img-preview">
            <img src="{{ $img['url'] }}" alt="{{ $img['alt'] ?: 'Kein Alt-Text' }}" loading="lazy">
        </div>
        <div class="img-body">
            <div class="img-filename">{{ $img['fileName'] }}</div>
            <div class="img-alt {{ empty($img['alt']) ? 'missing' : '' }}" id="alt-display-{{ $idx }}">
                @if(empty($img['alt']))
                    ❌ Kein Alt-Text vorhanden
                @else
                    "{{ $img['alt'] }}"
                @endif
            </div>
            <div class="img-meta">
                <span>📐 {{ $img['mimeType'] }}</span>
                <span>💾 {{ round($img['fileSize'] / 1024, 1) }} KB</span>
                @if($img['productContext'])
                    <span>🛍️ {{ Str::limit($img['productContext'], 30) }}</span>
                @endif
            </div>
            <div class="img-actions">
                <button class="btn btn-secondary" onclick="toggleImgEdit({{ $idx }})">✏️ Bearbeiten</button>
                <button class="btn btn-primary"   onclick="generateAltText({{ $idx }})" id="gen-btn-{{ $idx }}">
                    ✨ KI generieren
                </button>
            </div>
        </div>
        <div class="img-edit" id="img-edit-{{ $idx }}"></div>
    </div>
    @endforeach
</div>
@endif
@endisset

{{-- Batch progress overlay (hidden by default) --}}
<div class="batch-overlay" id="batchOverlay" style="display:none;">
    <div style="font-size:13px;color:var(--text-1);" id="batchMsg">⏳ Generiere Alt-Texte…</div>
    <div class="batch-progress">
        <div class="batch-progress-fill" id="batchFill" style="width:0%"></div>
    </div>
</div>

@push('scripts')
<script>
const LANG_ID   = @json($selectedLang);
const LANG_NAME = @json($languages[$selectedLang] ?? '');
const PROJECT_NAME = @json($project->name ?? 'diesem Shop');
const DOMAIN    = @json($storefrontUrl);
const DOMAIN_NAME = @json($domainName);
const images    = @json($rows);

const ROUTES = {
    generate:   "{{ route('seo.alttext.generate', $project) }}",
    save:       "{{ route('seo.alttext.save', $project) }}",
    batchSave:  "{{ route('seo.alttext.batch', $project) }}",
};

const csrf = () => document.querySelector('meta[name=csrf-token]').content;
const esc  = t => { const d=document.createElement('div'); d.textContent=t; return d.innerHTML; };

// Tracks pending items to batch-save
const pendingItems = {};
function updatePendingCount() {
    const count = Object.keys(pendingItems).length;
    document.getElementById('pending-count').textContent = count;
    document.getElementById('batch-save-btn').disabled = count === 0;
}

// ── Edit panel ────────────────────────────────────────────────
function toggleImgEdit(idx) {
    const panel = document.getElementById('img-edit-' + idx);
    if (panel.style.display !== 'none' && panel.innerHTML) { panel.style.display='none'; return; }
    panel.style.display = 'block';
    renderImgEdit(idx, images[idx].alt || '', false);
}

function renderImgEdit(idx, altVal, fromAi) {
    const len = altVal.length;
    const cls = len >= 50 && len <= 125 ? 'good' : len > 125 ? 'over' : 'warn';

    document.getElementById('img-edit-' + idx).innerHTML = `
        <div>
            ${fromAi ? '<div class="ai-note">✨ Von GPT-4o Vision generiert – prüfen & speichern</div>' : ''}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
                <label style="font-size:11px;font-weight:500;color:var(--text-2);">Alt-Text</label>
                <span id="altc-${idx}" class="char-info ${cls}">${len}/125</span>
            </div>
            <input type="text" class="ep-input" id="altinput-${idx}"
                   value="${esc(altVal)}" maxlength="200"
                   oninput="updateAltCounter(${idx})">
            <div style="font-size:11px;color:var(--text-3);margin-top:4px;">
                Ideal: 50–125 Zeichen · Beschreibend · Keyword-optimiert
            </div>
            <div style="display:flex;gap:7px;margin-top:10px;">
                <button class="btn btn-primary" onclick="saveAltText(${idx},'${images[idx].id}')" style="flex:1;padding:6px 10px;font-size:12px;">
                    💾 Speichern
                </button>
                <button class="btn btn-secondary" onclick="queueAltText(${idx})" style="padding:6px 10px;font-size:12px;">
                    📋 In Warteschlange
                </button>
                <button class="btn btn-secondary" onclick="document.getElementById('img-edit-${idx}').style.display='none'" style="padding:6px 10px;font-size:12px;">
                    ✕
                </button>
            </div>
        </div>`;
}

// ── AI Generate ───────────────────────────────────────────────
async function generateAltText(idx) {
    const img = images[idx];
    const btn = document.getElementById('gen-btn-' + idx);
    btn.disabled = true; btn.textContent = '⏳ KI analysiert…';

    const panel = document.getElementById('img-edit-' + idx);
    panel.style.display = 'block';
    panel.innerHTML = '<div style="padding:10px;font-size:12px;color:var(--text-2);">🤖 GPT-4o Vision analysiert das Bild…</div>';

    const body = {
        imageUrl:           img.url,
        fileName:           img.fileName,
        productContext:     img.productContext || '',
        customInstructions: document.getElementById('customPrompt').value.trim(),
        targetLang:         LANG_NAME,
        domain:             DOMAIN_NAME,
    };

    try {
        const res  = await fetch(ROUTES.generate, {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
            body: JSON.stringify(body),
        });
        const data = await res.json();

        if (!data.success) {
            panel.innerHTML = `<div style="padding:10px;font-size:12px;color:var(--danger)">❌ ${esc(data.error)}</div>`;
            btn.disabled=false; btn.textContent='✨ KI generieren';
            return;
        }

        const altText = data.altText || '';
        images[idx]._generated = altText;
        renderImgEdit(idx, altText, true);

        // Auto-queue
        pendingItems[idx] = { mediaId: img.id, langId: LANG_ID, alt: altText };
        updatePendingCount();

    } catch(e) {
        panel.innerHTML = `<div style="padding:10px;font-size:12px;color:var(--danger)">❌ ${esc(e.message)}</div>`;
    }

    btn.disabled=false; btn.textContent='✨ KI generieren';
}

// ── Save single ───────────────────────────────────────────────
async function saveAltText(idx, mediaId) {
    const alt = document.getElementById('altinput-' + idx)?.value || '';

    const res  = await fetch(ROUTES.save, {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
        body: JSON.stringify({ mediaId, langId:LANG_ID, alt }),
    });
    const data = await res.json();

    if (data.success) {
        images[idx].alt = alt;

        // Update display
        const display = document.getElementById('alt-display-' + idx);
        display.className = 'img-alt';
        display.textContent = '"' + alt + '"';
        document.getElementById('img-edit-' + idx).style.display = 'none';

        // Remove from pending
        delete pendingItems[idx];
        updatePendingCount();

        showToast('✅ Alt-Text gespeichert!');
    } else {
        alert('❌ ' + (data.error || 'Fehler'));
    }
}

// ── Queue for batch ───────────────────────────────────────────
function queueAltText(idx) {
    const alt = document.getElementById('altinput-' + idx)?.value || '';
    if (!alt) return;
    pendingItems[idx] = { mediaId: images[idx].id, langId: LANG_ID, alt };
    updatePendingCount();
    showToast('📋 In Warteschlange hinzugefügt');
}

// ── Batch save ────────────────────────────────────────────────
async function batchSave() {
    const items = Object.values(pendingItems);
    if (!items.length) return;

    const btn = document.getElementById('batch-save-btn');
    btn.disabled = true; btn.textContent = '⏳ Speichere…';

    const res  = await fetch(ROUTES.batchSave, {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
        body: JSON.stringify({ items }),
    });
    const data = await res.json();

    if (data.success) {
        // Update displays
        Object.entries(pendingItems).forEach(([idx, item]) => {
            images[idx].alt = item.alt;
            const display = document.getElementById('alt-display-' + idx);
            if(display) { display.className='img-alt'; display.textContent='"'+item.alt+'"'; }
        });
        Object.keys(pendingItems).forEach(k => delete pendingItems[k]);
        updatePendingCount();
        showToast(`✅ ${data.saved} gespeichert${data.failed ? ', ' + data.failed + ' Fehler' : ''}!`);
    } else {
        alert('❌ ' + (data.error || 'Fehler'));
    }

    btn.disabled = false;
    btn.innerHTML = '💾 Alle speichern (<span id="pending-count">0</span>)';
}

// ── Generate All ──────────────────────────────────────────────
async function generateAll() {
    const missing = images.filter(img => !img.alt);
    if (!missing.length) { showToast('✅ Alle Bilder haben bereits Alt-Texte!'); return; }

    const overlay = document.getElementById('batchOverlay');
    overlay.style.display = 'block';

    let done=0; const total=missing.length;

    for (let i=0; i<images.length; i++) {
        if (!images[i].alt) {
            done++;
            document.getElementById('batchMsg').textContent  = `⏳ KI generiert ${done}/${total}…`;
            document.getElementById('batchFill').style.width = (done/total*100) + '%';
            await generateAltText(i);
            await new Promise(r => setTimeout(r, 500)); // rate limiting
        }
    }

    document.getElementById('batchMsg').textContent  = `✅ ${done} Alt-Texte generiert! Jetzt speichern.`;
    document.getElementById('batchFill').style.width = '100%';
    setTimeout(() => overlay.style.display='none', 4000);
}

// ── Helpers ───────────────────────────────────────────────────
function updateAltCounter(idx) {
    const input = document.getElementById('altinput-' + idx);
    const len   = input.value.length;
    const el    = document.getElementById('altc-' + idx);
    el.textContent = len + '/125';
    el.className   = 'char-info ' + (len >= 50 && len <= 125 ? 'good' : len > 125 ? 'over' : 'warn');
}

function showToast(msg) {
    const t=document.createElement('div');
    t.style.cssText='position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:rgba(124,58,237,0.9);border:1px solid rgba(124,58,237,0.6);color:#fff;padding:10px 20px;border-radius:8px;font-size:13px;z-index:998;box-shadow:0 4px 12px rgba(124,58,237,0.3);';
    t.textContent=msg; document.body.appendChild(t); setTimeout(()=>t.remove(),3000);
}

// ── Reset Prompt ─────────────────────────────────────────────────
function resetPrompt() {
    const defaultPrompt = `Erstelle präzise, SEO-optimierte Alt-Texte (50–125 Zeichen) für Produkte aus "${PROJECT_NAME}". Beschreibe was sichtbar ist, integriere relevante Keywords natürlich. Kein "Bild von" oder "Zeigt". Sprache: ${LANG_NAME}.`;
    
    document.getElementById('customPrompt').value = defaultPrompt;
    showToast('🔄 Prompt auf Standard zurückgesetzt');
}
</script>
@endpush

</x-layouts.app>

<x-layouts.app title="Produkte SEO – {{ $project->name }}">

@push('styles')
<style>
/* ── Toolbar ──────────────────────────────────────────── */
.seo-toolbar {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 0; margin-bottom: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    flex-wrap: wrap;
}
.seo-select {
    height: 36px; padding: 0 10px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px; color: var(--text-1);
    font-size: 13px; font-family: inherit; outline: none;
}
.seo-select:focus { border-color: var(--accent); }
.seo-input {
    height: 36px; padding: 0 10px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px; color: var(--text-1);
    font-size: 13px; font-family: inherit; outline: none;
    width: 200px;
}
.seo-input:focus { border-color: var(--accent); }
.toolbar-right { margin-left: auto; display: flex; gap: 8px; }

/* ── Opt Checkboxes ───────────────────────────────────── */
.opt-group {
    display: flex; align-items: center; gap: 16px;
    padding: 10px 14px;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px; margin-bottom: 20px;
    font-size: 12px; color: var(--text-2); flex-wrap: wrap;
}
.opt-check { display: flex; align-items: center; gap: 6px; cursor: pointer; }
.opt-check input { width: 15px; height: 15px; accent-color: var(--accent); cursor: pointer; }

/* ── Result Row ───────────────────────────────────────── */
.result-row {
    margin-bottom: 32px;
    padding-bottom: 28px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.result-breadcrumb {
    font-size: 12px; color: var(--text-3);
    margin-bottom: 3px; display: flex; align-items: center; gap: 6px;
}
.result-title-link {
    font-size: 19px; font-weight: 500;
    color: #8ab4f8; text-decoration: none; line-height: 1.3;
}
.result-title-link:hover { text-decoration: underline; }
.result-desc {
    font-size: 13px; color: var(--text-2); margin-top: 4px;
    line-height: 1.6; max-width: 620px;
}
.result-meta {
    display: flex; gap: 10px; margin-top: 8px;
    font-size: 11px; color: var(--text-3); flex-wrap: wrap;
}
.result-meta span { display: flex; align-items: center; gap: 4px; }

.result-actions { display: flex; gap: 8px; margin-top: 10px; }
.result-actions .btn { padding: 5px 12px; font-size: 12px; }

/* ── Edit panel ───────────────────────────────────────── */
.edit-panel {
    margin-top: 16px;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px; padding: 20px;
}
.ep-field { margin-bottom: 16px; }
.ep-label { font-size: 12px; font-weight: 500; color: var(--text-2); margin-bottom: 5px; display: flex; justify-content: space-between; }
.ep-input, .ep-textarea {
    width: 100%; padding: 9px 12px;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 8px; color: #000;
    font-family: inherit; font-size: 13px; outline: none;
    transition: border-color 0.2s;
}
.ep-textarea { resize: vertical; min-height: 100px; }
.ep-input:focus, .ep-textarea:focus { border-color: #7c3aed; box-shadow: 0 0 0 2px rgba(124,58,237,0.2); }
.char-bar {
    height: 3px; border-radius: 99px; margin-top: 4px;
    background: rgba(255,255,255,0.08); overflow: hidden;
}
.char-bar-fill { height: 100%; border-radius: 99px; transition: width 0.2s, background 0.2s; }
.char-hint { font-size: 11px; color: var(--text-3); margin-top: 3px; display: flex; justify-content: space-between; }
.char-hint.good { color: var(--success); }
.char-hint.warn { color: var(--warning); }
.char-hint.over { color: var(--danger); }

.seo-preview-box {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px; padding: 14px;
    margin-top: 8px; font-size: 13px; line-height: 1.7;
}
.seo-preview-box h2,h3 { margin: 12px 0 6px; }
.seo-preview-box p    { margin: 6px 0; }
.seo-preview-box ul   { margin: 6px 0 6px 18px; }

.ai-note {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 12px; border-radius: 6px;
    background: rgba(124,58,237,0.15); border: 1px solid rgba(124,58,237,0.4);
    color: #a78bfa;
    font-size: 11px; font-weight: 500;
    margin-bottom: 12px;
}

/* Stats bar */
.stats-bar {
    display: flex; gap: 16px; padding: 10px 14px;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px; font-size: 12px; color: var(--text-2);
    margin-bottom: 22px; flex-wrap: wrap;
}
.stats-bar strong { color: var(--text-1); }
</style>
@endpush

<div class="page-header">
    <div>
        <div class="page-title">🏷️ Produkte SEO</div>
        <div class="page-subtitle">{{ $project->name }} · {{ count($rows) }} Produkte geladen</div>
    </div>
</div>

{{-- ── Toolbar ──────────────────────────────────────────────── --}}
<form method="GET" id="filterForm">
    <input type="hidden" name="sc" id="sc-hidden" value="{{ $selectedSc }}">
    <div class="seo-toolbar">
        <select class="seo-select" name="sc" onchange="document.getElementById('sc-hidden').value=this.value;this.form.submit()">
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
        <input type="text" class="seo-input" name="search"
               value="{{ $search }}" placeholder="🔍 Produkt suchen…"
               onkeydown="if(event.key==='Enter')this.form.submit()">
        <input type="number" class="seo-input" name="max" value="{{ $limit }}" style="width:80px">
        <button type="submit" class="btn btn-primary" style="padding:7px 14px;font-size:13px;">Laden</button>
        <div class="toolbar-right">
            <button type="button" class="btn btn-secondary" style="padding:7px 14px;font-size:13px;" onclick="generateAll()">
                ✨ Alle optimieren
            </button>
        </div>
    </div>
</form>

{{-- ── Optimization Scope ───────────────────────────────────── --}}
<div class="opt-group">
    <span style="color:var(--text-3);">Optimiere:</span>
    <label class="opt-check"><input type="checkbox" id="opt-title" checked> 📝 Meta Title</label>
    <label class="opt-check"><input type="checkbox" id="opt-desc"  checked> 📄 Meta Description</label>
    <label class="opt-check"><input type="checkbox" id="opt-text"  checked> 📋 Produktbeschreibung</label>
    <div style="margin-left:auto; font-size:11px; color:var(--text-3);">
        Gilt für alle ✨-Buttons auf dieser Seite
    </div>
</div>

{{-- AI Instructions textarea --}}
<details style="margin-bottom:20px;" open>
    <summary style="font-size:12px;color:var(--text-3);cursor:pointer;padding:6px 0;">⚙️ KI-Anweisungen anpassen</summary>
    <textarea id="customPrompt" rows="8" class="form-input" style="margin-top:8px;font-size:12px;resize:vertical;"
              placeholder="Passe die KI-Anweisungen an deine Brand-Anforderungen an…">{{ $project->seo_prompt ?? 'Du bist ein SEO-Experte für Shopware-Shops. Erstelle für das Produkt "' . ($project->name ?? 'diesem Shop') . '" optimierte SEO-Texte.

Berücksichtige dabei:
• Zielgruppe: Kunden, die nach Produkten wie diesem suchen
• Keywords: Relevante Suchbegriffe, die Kunden verwenden würden
• Shop-Kontext: Produkte aus dem Sortiment von ' . ($project->name ?? 'diesem Shop') . '
• Brand-Voice: Professionell, vertrauenswürdig und kundenorientiert
• Länge: Meta-Titel 50-60 Zeichen, Meta-Beschreibung 150-160 Zeichen
• Call-to-Action: Klare Handlungsaufforderung zum Kauf

Fokus auf Conversion und hohe Klickraten in Suchmaschinen.' }}</textarea>
    <div style="display:flex;gap:8px;margin-top:6px;align-items:center;">
        <button onclick="savePrompt()" class="ep-btn ep-btn-primary" style="font-size:12px;padding:4px 12px;">💾 Prompt speichern</button>
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
{{-- Stats --}}
@if($rows)
<div class="stats-bar">
    <span>Produkte: <strong>{{ count($rows) }}</strong></span>
    <span>Mit Meta Title: <strong>{{ count(array_filter($rows, fn($r) => !empty($r['title']))) }}</strong></span>
    <span>Mit Beschreibung: <strong>{{ count(array_filter($rows, fn($r) => !empty($r['description']))) }}</strong></span>
    <span>Sprache: <strong>{{ $languages[$selectedLang] ?? $selectedLang }}</strong></span>
</div>
@endif

{{-- ── Results ──────────────────────────────────────────────── --}}
@forelse($rows as $idx => $prod)
<div class="result-row" id="row-{{ $idx }}">
    <div class="result-breadcrumb">
        <span>{{ $domainName }}</span>
        @if($prod['url'])
            <span>›</span>
            <a href="{{ $prod['url'] }}" target="_blank" style="color:var(--text-3);text-decoration:none;max-width:400px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                {{ $prod['url'] }}
            </a>
        @endif
    </div>

    <div>
        @if($prod['url'])
            <a href="{{ $prod['url'] }}" target="_blank" class="result-title-link">
                {{ $prod['title'] ?: $prod['name'] }}
            </a>
        @else
            <span class="result-title-link" style="cursor:pointer" onclick="toggleEdit({{ $idx }})">
                {{ $prod['title'] ?: $prod['name'] }}
            </span>
        @endif
    </div>

    <div class="result-desc">
        @if($prod['metaDesc'])
            {{ $prod['metaDesc'] }}
        @else
            <span style="color:var(--text-3);font-style:italic;">Keine Meta-Description vorhanden</span>
        @endif
    </div>

    <div class="result-meta">
        <span>🔢 {{ $prod['productNumber'] }}</span>
        @if($prod['description']) <span>✅ Beschreibung</span> @endif
        @if(!$prod['title']) <span style="color:var(--warning)">⚠️ Kein Title</span> @endif
    </div>

    <div class="result-actions">
        @if($prod['url'])
            <button class="btn btn-secondary" onclick="analyzePage({{ $idx }}, '{{ addslashes($prod['url']) }}')">
                🔍 Analysieren
            </button>
        @endif
        <button class="btn btn-secondary" onclick="toggleEdit({{ $idx }})">✏️ Bearbeiten</button>
        <button class="btn btn-primary"   onclick="optimizeProduct({{ $idx }})">✨ Optimieren</button>
    </div>

    <div id="edit-{{ $idx }}" style="display:none;"></div>
</div>
@empty
<div style="text-align:center;padding:60px;color:var(--text-3);">
    <div style="font-size:40px;margin-bottom:12px;">🛍️</div>
    <div style="font-size:15px;color:var(--text-2);">Keine Produkte gefunden</div>
    <div style="font-size:13px;margin-top:6px;">Lade den Sales Channel oder passe die Suche an.</div>
</div>
@endforelse
@endif
@endisset

@push('scripts')
<script>
const LANG_ID   = @json($selectedLang);
const LANG_NAME = @json($languages[$selectedLang] ?? '');
const PROJECT_NAME = @json($project->name ?? 'diesem Shop');
const DOMAIN    = @json($storefrontUrl);
const products  = @json($rows);

const ROUTES = {
    analyze:  "{{ route('seo.products.analyze', $project) }}",
    generate: "{{ route('seo.products.generate', $project) }}",
    save:     "{{ route('seo.products.save', $project) }}",
    prompt:   "{{ route('seo.prompt', $project) }}",
    prompt:   "{{ route('seo.prompt', $project) }}",
};

async function savePrompt() {
    const prompt = document.getElementById('customPrompt').value.trim();
    const status = document.getElementById('prompt-status');
    status.textContent = '⏳ Speichern...';
    const res = await fetch(ROUTES.prompt, {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({prompt})});
    const data = await res.json();
    status.textContent = data.success ? '✅ Gespeichert!' : '❌ Fehler';
    setTimeout(() => status.textContent = '', 3000);
}
,body:JSON.stringify({prompt})});
    const data = await res.json();
    status.textContent = data.success ? '✅ Gespeichert!' : '❌ Fehler';
    setTimeout(() => status.textContent = '', 3000);
}
const csrf = () => document.querySelector('meta[name=csrf-token]').content;
const esc  = t => { const d=document.createElement('div'); d.textContent=t; return d.innerHTML; };

// ── Edit panel ────────────────────────────────────────────────
function toggleEdit(idx) {
    const panel = document.getElementById('edit-' + idx);
    if (panel.style.display !== 'none' && panel.innerHTML) { panel.style.display='none'; return; }
    panel.style.display = 'block';
    renderEditPanel(idx, products[idx]);
}

function renderEditPanel(idx, p, aiData = null, analysed = false) {
    const titleVal = (aiData?.title   ?? p.title)       || '';
    const descVal  = (aiData?.metaDesc ?? p.metaDesc)   || '';
    const seoVal   = (aiData?.seoText ?? p.description) || '';
    const tl = titleVal.length, dl = descVal.length;
    const seoTxt = seoVal.replace(/<[^>]*>/g,'').length;

    document.getElementById('edit-' + idx).innerHTML = `
    <div class="edit-panel">
        ${aiData ? '<div class="ai-note">✨ Von KI generiert – bitte prüfen und ggf. anpassen</div>' : ''}
        ${analysed ? '<div class="ai-note" style="background:rgba(14,165,233,0.08);border-color:rgba(14,165,233,0.25);color:#38bdf8;">🔍 Basierend auf Seitenanalyse optimiert</div>' : ''}

        <div class="ep-field">
            <div class="ep-label">
                <span>Meta Title <span style="color:var(--text-3)">(metaTitle)</span></span>
                <span id="tc-${idx}" class="char-hint ${tl>60?'over':tl>50?'good':''}">${tl}/60</span>
            </div>
            <input type="text" class="ep-input" id="title-${idx}"
                   value="${esc(titleVal)}" maxlength="100" oninput="updateCounter(${idx},'title',60)">
            <div class="char-bar"><div class="char-bar-fill" id="tb-${idx}" style="width:${Math.min(tl/60*100,100)}%;background:${tl>60?'var(--danger)':tl>50?'var(--success)':'var(--warning)'}"></div></div>
        </div>

        <div class="ep-field">
            <div class="ep-label">
                <span>Meta Description</span>
                <span id="dc-${idx}" class="char-hint ${dl>155?'over':dl>130?'good':''}">${dl}/155</span>
            </div>
            <textarea class="ep-textarea" id="desc-${idx}" rows="3" maxlength="300"
                      oninput="updateCounter(${idx},'desc',155)">${esc(descVal)}</textarea>
            <div class="char-bar"><div class="char-bar-fill" id="db-${idx}" style="width:${Math.min(dl/155*100,100)}%;background:${dl>155?'var(--danger)':dl>130?'var(--success)':'var(--warning)'}"></div></div>
        </div>

        <div class="ep-field">
            <div class="ep-label">
                <span>Produktbeschreibung <span style="color:var(--text-3)">(description)</span></span>
                <span id="sc-${idx}" class="char-hint ${seoTxt>=200&&seoTxt<=400?'good':'warn'}">~${seoTxt} Zeichen</span>
            </div>
            <textarea class="ep-textarea" id="seotext-${idx}" rows="8"
                      oninput="updateSeoPreview(${idx})">${esc(seoVal)}</textarea>
            ${seoVal ? `<div class="seo-preview-box" id="preview-${idx}">${seoVal}</div>` : ''}
        </div>

        <div style="display:flex;gap:8px;margin-top:4px;">
            <button class="btn btn-primary" onclick="saveProduct(${idx},'${products[idx].id}')" style="flex:1;padding:8px;">
                💾 In Shopware speichern
            </button>
            <button class="btn btn-secondary" onclick="document.getElementById('edit-${idx}').style.display='none'" style="padding:8px 14px;">
                Schließen
            </button>
        </div>
    </div>`;
}

// ── Analyze ───────────────────────────────────────────────────
async function analyzePage(idx, url) {
    const row = document.getElementById('row-' + idx);
    const btn = row.querySelectorAll('.result-actions .btn')[0];
    btn.disabled = true; btn.textContent = '⏳ Analysiere…';

    const res  = await fetch(ROUTES.analyze + '?url=' + encodeURIComponent(url), { headers: { 'X-CSRF-TOKEN': csrf() } });
    const data = await res.json();

    // Store for later use
    products[idx]._scraped = data;

    btn.disabled = false; btn.textContent = '✅ Analysiert';
    setTimeout(() => btn.textContent = '🔍 Analysieren', 2500);
}

// ── Optimize (generate + open panel) ─────────────────────────
async function optimizeProduct(idx) {
    const p        = products[idx];
    const panel    = document.getElementById('edit-' + idx);
    panel.style.display = 'block';
    panel.innerHTML = '<div class="edit-panel"><div style="color:var(--text-2);font-size:13px;">⏳ KI analysiert…</div></div>';

    // Auto-analyze if URL exists and not yet scraped
    let scraped = p._scraped || null;
    if (p.url && !scraped) {
        const r = await fetch(ROUTES.analyze + '?url=' + encodeURIComponent(p.url), { headers: {'X-CSRF-TOKEN': csrf()} });
        scraped = (await r.json());
        products[idx]._scraped = scraped;
    }

    const generate = [];
    if (document.getElementById('opt-title').checked) generate.push('title');
    if (document.getElementById('opt-desc').checked)  generate.push('desc');
    if (document.getElementById('opt-text').checked)  generate.push('text');

    if (!generate.length) { alert('Bitte mindestens eine Option auswählen.'); return; }

    const body = {
        name:               p.name,
        productNumber:      p.productNumber,
        content:            scraped?.content || '',
        h1:                 scraped?.h1      || '',
        price:              scraped?.price   || '',
        features:           (scraped?.features || []).join(', '),
        customInstructions: document.getElementById('customPrompt').value.trim(),
        targetLang:         LANG_NAME,
        domain:             DOMAIN,
        generate,
        _token: csrf(),
    };

    try {
        const res  = await fetch(ROUTES.generate, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()}, body: JSON.stringify(body) });
        const data = await res.json();

        if (!data.success) { panel.innerHTML = `<div class="edit-panel" style="color:var(--danger)">❌ ${esc(data.error)}</div>`; return; }

        renderEditPanel(idx, p, data, !!scraped?.content);
    } catch(e) {
        panel.innerHTML = `<div class="edit-panel" style="color:var(--danger)">❌ Netzwerkfehler: ${esc(e.message)}</div>`;
    }
}

// ── Save ──────────────────────────────────────────────────────
async function saveProduct(idx, productId) {
    const title   = document.getElementById('title-'   + idx)?.value || '';
    const metaDesc = document.getElementById('desc-'   + idx)?.value || '';
    const seoText = document.getElementById('seotext-' + idx)?.value || '';

    const res  = await fetch(ROUTES.save, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
        body:    JSON.stringify({ productId, langId: LANG_ID, title, metaDesc, seoText }),
    });
    const data = await res.json();

    if (data.success) {
        // Update local state
        products[idx] = { ...products[idx], title, metaDesc, description: seoText };

        // Update display
        const row      = document.getElementById('row-' + idx);
        row.querySelector('.result-title-link').textContent = title || products[idx].name;
        row.querySelector('.result-desc').textContent       = metaDesc || '';

        document.getElementById('edit-' + idx).style.display = 'none';
        showToast('✅ Gespeichert in Shopware!');
    } else {
        alert('❌ ' + (data.error || 'Unbekannter Fehler'));
    }
}

// ── Batch optimize ────────────────────────────────────────────
async function generateAll() {
    const btn = event.target;
    btn.disabled = true;
    let done = 0;
    const total = products.length;

    for (let i = 0; i < products.length; i++) {
        btn.textContent = `⏳ ${++done}/${total}…`;
        await optimizeProduct(i);
        await new Promise(r => setTimeout(r, 800)); // rate limit
    }

    btn.disabled = false;
    btn.textContent = `✅ ${total} optimiert!`;
    setTimeout(() => btn.textContent = '✨ Alle optimieren', 3000);
}

// ── Counter / Preview helpers ─────────────────────────────────
function updateCounter(idx, type, max) {
    const input = document.getElementById((type==='title'?'title':'desc') + '-' + idx);
    const len   = input.value.length;
    const el    = document.getElementById((type==='title'?'tc':'dc') + '-' + idx);
    const bar   = document.getElementById((type==='title'?'tb':'db') + '-' + idx);
    el.textContent = len + '/' + max;
    el.className   = 'char-hint ' + (len > max ? 'over' : len > max*0.85 ? 'good' : '');
    bar.style.width      = Math.min(len/max*100, 100) + '%';
    bar.style.background = len > max ? 'var(--danger)' : len > max*0.85 ? 'var(--success)' : 'var(--warning)';
}

function updateSeoPreview(idx) {
    const text    = document.getElementById('seotext-' + idx).value;
    const preview = document.getElementById('preview-' + idx);
    if (preview) preview.innerHTML = text;
    const len = text.replace(/<[^>]*>/g,'').length;
    document.getElementById('sc-' + idx).textContent = '~' + len + ' Zeichen';
    document.getElementById('sc-' + idx).className   = 'char-hint ' + (len >= 200 && len <= 400 ? 'good' : 'warn');
}

// ── Toast ──────────────────────────────────────────────────────
function showToast(msg) {
    const t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:rgba(124,58,237,0.9);border:1px solid rgba(124,58,237,0.6);color:#fff;padding:10px 18px;border-radius:8px;font-size:13px;z-index:999;box-shadow:0 4px 12px rgba(124,58,237,0.3);';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}

// ── Reset Prompt ─────────────────────────────────────────────────
function resetPrompt() {
    const defaultPrompt = `Du bist ein SEO-Experte für Shopware-Shops. Erstelle für das Produkt "${PROJECT_NAME}" optimierte SEO-Texte.

Berücksichtige dabei:
• Zielgruppe: Kunden, die nach Produkten wie diesem suchen
• Keywords: Relevante Suchbegriffe, die Kunden verwenden würden
• Shop-Kontext: Produkte aus dem Sortiment von ${PROJECT_NAME}
• Brand-Voice: Professionell, vertrauenswürdig und kundenorientiert
• Länge: Meta-Titel 50-60 Zeichen, Meta-Beschreibung 150-160 Zeichen
• Call-to-Action: Klare Handlungsaufforderung zum Kauf

Fokus auf Conversion und hohe Klickraten in Suchmaschinen.`;
    
    document.getElementById('customPrompt').value = defaultPrompt;
    showToast('🔄 Prompt auf Standard zurückgesetzt');
}
</script>
@endpush

</x-layouts.app>

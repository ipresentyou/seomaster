<x-layouts.app title="Kategorien SEO – {{ $project->name }}">

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

.opt-group {
    display:flex;align-items:center;gap:16px;padding:10px 14px;
    background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);
    border-radius:8px;margin-bottom:20px;font-size:12px;color:var(--text-2);flex-wrap:wrap;
}
.opt-check { display:flex;align-items:center;gap:6px;cursor:pointer; }
.opt-check input { width:15px;height:15px;accent-color:var(--accent);cursor:pointer; }

.result-row {
    margin-bottom:30px;padding-bottom:26px;
    border-bottom:1px solid rgba(255,255,255,0.05);
}
.result-breadcrumb { font-size:12px;color:var(--text-3);margin-bottom:3px;display:flex;align-items:center;gap:6px; }
.result-title-link { font-size:19px;font-weight:500;color:#8ab4f8;text-decoration:none;line-height:1.3; }
.result-title-link:hover { text-decoration:underline; }
.result-desc { font-size:13px;color:var(--text-2);margin-top:4px;line-height:1.6;max-width:620px; }
.result-meta { display:flex;gap:10px;margin-top:8px;font-size:11px;color:var(--text-3);flex-wrap:wrap; }
.result-meta span { display:flex;align-items:center;gap:4px; }
.result-actions { display:flex;gap:8px;margin-top:10px; }
.result-actions .btn { padding:5px 12px;font-size:12px; }

.analysis-box {
    margin-top:10px;padding:12px 14px;
    background:rgba(14,165,233,0.06);border:1px solid rgba(14,165,233,0.2);
    border-radius:8px;font-size:12px;color:var(--text-2);
}
.analysis-item { margin:3px 0;display:flex;gap:8px; }
.analysis-item .akey { color:var(--text-3);min-width:110px;flex-shrink:0; }
.analysis-item.ok   .akey { color:var(--success); }
.analysis-item.warn .akey { color:var(--warning); }
.analysis-item.err  .akey { color:var(--danger); }

.edit-panel {
    margin-top:16px;background:rgba(255,255,255,0.02);
    border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:20px;
}
.ep-field { margin-bottom:16px; }
.ep-label { font-size:12px;font-weight:500;color:var(--text-2);margin-bottom:5px;display:flex;justify-content:space-between; }
.ep-input,.ep-textarea {
    width:100%;padding:9px 12px;background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,255,255,0.1);border-radius:8px;
    color:var(--text-1);font-family:inherit;font-size:13px;outline:none;transition:border-color 0.2s;
}
.ep-textarea { resize:vertical;min-height:90px; }
.ep-input:focus,.ep-textarea:focus { border-color:var(--accent);box-shadow:0 0 0 2px var(--accent-glow); }
.char-bar { height:3px;border-radius:99px;margin-top:4px;background:rgba(255,255,255,0.08);overflow:hidden; }
.char-bar-fill { height:100%;border-radius:99px;transition:width 0.2s,background 0.2s; }
.char-hint { font-size:11px;color:var(--text-3);margin-top:3px;display:flex;justify-content:space-between; }
.char-hint.good { color:var(--success); }
.char-hint.warn { color:var(--warning); }
.char-hint.over { color:var(--danger); }
.ai-note { display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:6px;background:rgba(124,58,237,0.08);border:1px solid rgba(124,58,237,0.2);font-size:12px;color:var(--accent-light);margin-bottom:14px; }
.seo-preview-box { background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:8px;padding:14px;margin-top:8px;font-size:13px;line-height:1.7; }
.seo-preview-box h2,h3 { margin:12px 0 6px; }
.seo-preview-box p { margin:6px 0; }
.seo-preview-box ul { margin:6px 0 6px 18px; }

.stats-bar {
    display:flex;gap:16px;padding:10px 14px;background:rgba(255,255,255,0.02);
    border:1px solid rgba(255,255,255,0.06);border-radius:8px;font-size:12px;
    color:var(--text-2);margin-bottom:22px;flex-wrap:wrap;
}
.stats-bar strong { color:var(--text-1); }
</style>
@endpush

<div class="page-header">
    <div>
        <div class="page-title">📁 Kategorien SEO</div>
        <div class="page-subtitle">{{ $project->name }} · {{ count($rows) }} Kategorien geladen</div>
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
        <input type="number" class="seo-input" name="max" value="{{ $limit }}">
        <button type="submit" class="btn btn-primary" style="padding:7px 14px;font-size:13px;">Laden</button>
        <div class="toolbar-right">
            <button type="button" class="btn btn-secondary" style="padding:7px 14px;font-size:13px;" onclick="analyzeAll()">
                🔍 Alle analysieren
            </button>
            <button type="button" class="btn btn-secondary" style="padding:7px 14px;font-size:13px;" onclick="generateAll()">
                ✨ Alle optimieren
            </button>
        </div>
    </div>
</form>

{{-- Optimization Scope --}}
<div class="opt-group">
    <span style="color:var(--text-3)">Optimiere:</span>
    <label class="opt-check"><input type="checkbox" id="opt-title"    checked> 📝 Meta Title</label>
    <label class="opt-check"><input type="checkbox" id="opt-desc"     checked> 📄 Meta Description</label>
    <label class="opt-check"><input type="checkbox" id="opt-keywords" checked> 🔑 Keywords</label>
    <label class="opt-check"><input type="checkbox" id="opt-text"     checked> 📋 SEO-Text</label>
</div>

<details style="margin-bottom:20px;" open>
    <summary style="font-size:12px;color:var(--text-3);cursor:pointer;padding:6px 0;">⚙️ KI-Anweisungen anpassen</summary>
    <textarea id="customPrompt" rows="8" class="form-input" style="margin-top:8px;font-size:12px;resize:vertical;"
>{{ $project->seo_prompt ?? '' }}</textarea>
    <div style="display:flex;gap:8px;margin-top:6px;align-items:center;">
        <button onclick="savePrompt()" class="ep-btn ep-btn-primary" style="font-size:12px;padding:4px 12px;">💾 Prompt speichern</button>
        <span id="prompt-status" style="font-size:11px;color:var(--text-3);"></span>
    </div>
    <div style="font-size:11px;color:var(--text-3);margin-top:4px;">
        Domain: <strong>{{ $storefrontDomain ?? '' }}</strong> · Sprache: <strong>{{ $languages[$selectedLang] ?? '' }}</strong>
    </div>
</details>

@if($rows)
<div class="stats-bar">
    <span>Kategorien: <strong>{{ count($rows) }}</strong></span>
    <span>Mit Meta Title: <strong>{{ count(array_filter($rows, fn($r)=>!empty($r['title']))) }}</strong></span>
    <span>Mit Keywords: <strong>{{ count(array_filter($rows, fn($r)=>!empty($r['keywords']))) }}</strong></span>
    <span>Mit SEO-Text: <strong>{{ count(array_filter($rows, fn($r)=>!empty($r['description']))) }}</strong></span>
    <span>Sprache: <strong>{{ $languages[$selectedLang] ?? $selectedLang }}</strong></span>
</div>
@endif

{{-- Results --}}
@forelse($rows as $idx => $cat)
<div class="result-row" id="row-{{ $idx }}">
    <div class="result-breadcrumb">
        <span>{{ $domainName }}</span>
        @if($cat['url'])
            <span>›</span>
            <a href="{{ $cat['url'] }}" target="_blank"
               style="color:var(--text-3);text-decoration:none;max-width:400px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                {{ $cat['url'] }}
            </a>
        @endif
    </div>

    <div>
        @if($cat['url'])
            <a href="{{ $cat['url'] }}" target="_blank" class="result-title-link">
                {{ $cat['title'] ?: $cat['name'] }}
            </a>
        @else
            <span class="result-title-link" style="cursor:pointer" onclick="toggleEdit({{ $idx }})">
                {{ $cat['title'] ?: $cat['name'] }}
            </span>
        @endif
    </div>

    <div class="result-desc">
        @if($cat['metaDesc'])
            {{ $cat['metaDesc'] }}
        @else
            <span style="color:var(--text-3);font-style:italic;">Keine Meta-Description vorhanden</span>
        @endif
    </div>

    <div class="result-meta">
        <span>📄 {{ $cat['type'] }}</span>
        @if($cat['keywords'])
            <span>🔑 {{ count(explode(',', $cat['keywords'])) }} Keywords</span>
        @else
            <span style="color:var(--warning)">⚠️ Keine Keywords</span>
        @endif
        @if($cat['description']) <span>✅ SEO-Text</span> @endif
        @if(!$cat['title']) <span style="color:var(--warning)">⚠️ Kein Title</span> @endif
    </div>

    <div id="analysis-{{ $idx }}" style="display:none;"></div>

    <div class="result-actions">
        @if($cat['url'])
            <button class="btn btn-secondary" onclick="analyzePage({{ $idx }}, '{{ addslashes($cat['url']) }}')">
                🔍 Analysieren
            </button>
        @endif
        <button class="btn btn-secondary" onclick="toggleEdit({{ $idx }})">✏️ Bearbeiten</button>
        <button class="btn btn-primary"   onclick="optimizeCategory({{ $idx }})">✨ Optimieren</button>
    </div>

    <div id="edit-{{ $idx }}" style="display:none;"></div>
</div>
@empty
<div style="text-align:center;padding:60px;color:var(--text-3);">
    <div style="font-size:40px;margin-bottom:12px;">📁</div>
    <div style="font-size:15px;color:var(--text-2);">Keine Kategorien gefunden</div>
</div>
@endforelse

@push('scripts')
<script>
const LANG_ID   = @json($selectedLang);
const LANG_NAME = @json($languages[$selectedLang] ?? '');
const DOMAIN    = @json($storefrontUrl);
const categories = @json($rows);

const ROUTES = {
    analyze:  "{{ route('seo.categories.analyze', $project) }}",
    generate: "{{ route('seo.categories.generate', $project) }}",
    save:     "{{ route('seo.categories.save', $project) }}",
    prompt:   "{{ route('seo.categories.prompt', $project) }}",
};

const csrf = () => document.querySelector('meta[name=csrf-token]').content;
const esc  = t => { const d=document.createElement('div'); d.textContent=t; return d.innerHTML; };

async function savePrompt() {
    const prompt = document.getElementById('customPrompt').value.trim();
    const status = document.getElementById('prompt-status');
    status.textContent = '⏳ Speichern...';
    const res = await fetch(ROUTES.prompt, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
        body: JSON.stringify({prompt})
    });
    const data = await res.json();
    status.textContent = data.success ? '✅ Gespeichert!' : '❌ Fehler';
    setTimeout(() => status.textContent = '', 3000);
}

function toggleEdit(idx) {
    const panel = document.getElementById('edit-' + idx);
    if (panel.style.display !== 'none' && panel.innerHTML) { panel.style.display='none'; return; }
    panel.style.display = 'block';
    renderEditPanel(idx, categories[idx]);
}

function renderEditPanel(idx, c, aiData=null, analysed=false) {
    const titleVal    = aiData?.title    ?? c.title    ?? '';
    const descVal     = aiData?.metaDesc ?? c.metaDesc ?? '';
    const kwVal       = aiData?.keywords ?? c.keywords ?? '';
    const seoVal      = aiData?.seoText  ?? c.description ?? '';
    const tl=titleVal.length, dl=descVal.length;
    const seoTxt = seoVal.replace(/<[^>]*>/g,'').length;

    document.getElementById('edit-' + idx).innerHTML = `
    <div class="edit-panel">
        ${aiData   ? '<div class="ai-note">✨ Von KI generiert – bitte prüfen</div>' : ''}
        ${analysed ? '<div class="ai-note" style="background:rgba(14,165,233,0.08);border-color:rgba(14,165,233,0.25);color:#38bdf8;">🔍 Basierend auf Seitenanalyse optimiert</div>' : ''}

        <div class="ep-field">
            <div class="ep-label">
                <span>Meta Title</span>
                <span id="tc-${idx}" class="char-hint ${tl>60?'over':tl>50?'good':''}">${tl}/60</span>
            </div>
            <input type="text" class="ep-input" id="title-${idx}" value="${esc(titleVal)}" maxlength="100"
                   oninput="updateCounter(${idx},'title',60)">
            <div class="char-bar"><div class="char-bar-fill" id="tb-${idx}"
                 style="width:${Math.min(tl/60*100,100)}%;background:${tl>60?'var(--danger)':tl>50?'var(--success)':'var(--warning)'}"></div></div>
        </div>

        <div class="ep-field">
            <div class="ep-label">
                <span>Meta Description</span>
                <span id="dc-${idx}" class="char-hint ${dl>155?'over':dl>130?'good':''}">${dl}/155</span>
            </div>
            <textarea class="ep-textarea" id="desc-${idx}" rows="3" maxlength="300"
                      oninput="updateCounter(${idx},'desc',155)">${esc(descVal)}</textarea>
            <div class="char-bar"><div class="char-bar-fill" id="db-${idx}"
                 style="width:${Math.min(dl/155*100,100)}%;background:${dl>155?'var(--danger)':dl>130?'var(--success)':'var(--warning)'}"></div></div>
        </div>

        <div class="ep-field">
            <div class="ep-label"><span>Keywords <span style="color:var(--text-3)">(kommagetrennt)</span></span></div>
            <input type="text" class="ep-input" id="keywords-${idx}" value="${esc(kwVal)}"
                   placeholder="keyword1, keyword2, keyword3">
            <div style="font-size:11px;color:var(--text-3);margin-top:3px;">5–10 relevante Keywords für diese Kategorie</div>
        </div>

        <div class="ep-field">
            <div class="ep-label">
                <span>SEO-Text <span style="color:var(--text-3)">(description)</span></span>
                <span id="sc-${idx}" class="char-hint ${seoTxt>=300&&seoTxt<=600?'good':'warn'}">~${seoTxt} Zeichen</span>
            </div>
            <textarea class="ep-textarea" id="seotext-${idx}" rows="8"
                      oninput="updateSeoPreview(${idx})">${esc(seoVal)}</textarea>
            ${seoVal ? `<div class="seo-preview-box" id="preview-${idx}">${seoVal}</div>` : ''}
        </div>

        <div style="display:flex;gap:8px;margin-top:4px;">
            <button class="btn btn-primary" onclick="saveCategory(${idx},'${categories[idx].id}')" style="flex:1;padding:8px;">
                💾 In Shopware speichern
            </button>
            <button class="btn btn-secondary" onclick="document.getElementById('edit-${idx}').style.display='none'" style="padding:8px 14px;">
                Schließen
            </button>
        </div>
    </div>`;
}

async function analyzePage(idx, url, silent=false) {
    const row = document.getElementById('row-' + idx);
    const analysisDiv = document.getElementById('analysis-' + idx);

    if (!silent) {
        const btn = row.querySelector('button[onclick*="analyzePage"]');
        if (btn) { btn.disabled=true; btn.textContent='⏳ Analysiere…'; }
        analysisDiv.style.display = 'block';
        analysisDiv.innerHTML = '<div class="analysis-box">🔍 Analysiere Seite…</div>';
    }

    const res  = await fetch(ROUTES.analyze + '?url=' + encodeURIComponent(url), { headers:{'X-CSRF-TOKEN':csrf()} });
    const data = await res.json();
    categories[idx]._scraped = data;

    if (!silent) {
        const tlCls = data.title?.length > 60 ? 'warn' : 'ok';
        const dlCls = data.metaDesc?.length > 155 ? 'warn' : 'ok';
        analysisDiv.innerHTML = `
            <div class="analysis-box">
                <div style="font-weight:600;color:var(--accent-light);margin-bottom:8px;">🔍 Analyse</div>
                <div class="analysis-item ${data.status===200?'ok':'err'}">
                    <span class="akey">HTTP Status</span><span>${data.status || '–'}</span>
                </div>
                ${data.title ? `<div class="analysis-item ${tlCls}"><span class="akey">&lt;title&gt;</span><span>${esc(data.title)} <small style="color:var(--text-3)">(${data.title.length} Zeichen)</small></span></div>` : ''}
                ${data.metaDesc ? `<div class="analysis-item ${dlCls}"><span class="akey">Meta Desc</span><span>${esc(data.metaDesc.substring(0,100))}… <small style="color:var(--text-3)">(${data.metaDesc.length})</small></span></div>` : ''}
                ${data.keywords ? `<div class="analysis-item ok"><span class="akey">Keywords</span><span>${esc(data.keywords.substring(0,80))}</span></div>` : '<div class="analysis-item warn"><span class="akey">Keywords</span><span>Nicht gefunden</span></div>'}
                ${data.h1 ? `<div class="analysis-item ok"><span class="akey">&lt;h1&gt;</span><span>${esc(data.h1)}</span></div>` : ''}
                ${data.content ? `<div class="analysis-item ok"><span class="akey">Content</span><span>${esc(data.content.substring(0,120))}…</span></div>` : ''}
            </div>`;
        document.getElementById('row-' + idx).querySelector('button[onclick*="analyzePage"]').disabled = false;
        document.getElementById('row-' + idx).querySelector('button[onclick*="analyzePage"]').textContent = '✅ Analysiert';
        setTimeout(() => {
            const b = document.getElementById('row-' + idx).querySelector('button[onclick*="analyzePage"]');
            if(b) b.textContent = '🔍 Analysieren';
        }, 2500);
    }
    return data;
}

async function optimizeCategory(idx) {
    const c     = categories[idx];
    const panel = document.getElementById('edit-' + idx);
    panel.style.display = 'block';
    panel.innerHTML = '<div class="edit-panel"><div style="color:var(--text-2);font-size:13px;">⏳ KI analysiert…</div></div>';

    let scraped = c._scraped || null;
    if (c.url && !scraped) {
        scraped = await analyzePage(idx, c.url, true);
    }

    const generate = [];
    if (document.getElementById('opt-title').checked)    generate.push('title');
    if (document.getElementById('opt-desc').checked)     generate.push('desc');
    if (document.getElementById('opt-keywords').checked) generate.push('keywords');
    if (document.getElementById('opt-text').checked)     generate.push('text');
    if (!generate.length) { alert('Bitte mindestens eine Option wählen.'); return; }

    const body = {
        name:               c.name,
        content:            scraped?.content          || '',
        h1:                 scraped?.h1               || '',
        existingKeywords:   scraped?.keywords         || c.keywords || '',
        customInstructions: document.getElementById('customPrompt').value.trim(),
        targetLang:         LANG_NAME,
        domain:             DOMAIN,
        generate,
    };

    try {
        const res  = await fetch(ROUTES.generate, {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify(body)});
        const data = await res.json();
        if (!data.success) { panel.innerHTML=`<div class="edit-panel" style="color:var(--danger)">❌ ${esc(data.error)}</div>`; return; }
        renderEditPanel(idx, c, data, !!scraped?.content);
    } catch(e) {
        panel.innerHTML = `<div class="edit-panel" style="color:var(--danger)">❌ ${esc(e.message)}</div>`;
    }
}

async function saveCategory(idx, categoryId) {
    const title    = document.getElementById('title-'    + idx)?.value || '';
    const metaDesc = document.getElementById('desc-'     + idx)?.value || '';
    const keywords = document.getElementById('keywords-' + idx)?.value || '';
    const seoText  = document.getElementById('seotext-'  + idx)?.value || '';

    const res  = await fetch(ROUTES.save, {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},
        body: JSON.stringify({ categoryId, langId:LANG_ID, title, metaDesc, keywords, seoText }),
    });
    const data = await res.json();

    if (data.success) {
        categories[idx] = { ...categories[idx], title, metaDesc, keywords, description:seoText };
        document.getElementById('row-' + idx).querySelector('.result-title-link').textContent = title || categories[idx].name;
        document.getElementById('row-' + idx).querySelector('.result-desc').textContent = metaDesc || '';
        document.getElementById('edit-' + idx).style.display = 'none';
        showToast('✅ Gespeichert in Shopware!');
    } else {
        alert('❌ ' + (data.error || 'Unbekannter Fehler'));
    }
}

async function analyzeAll() {
    const btn = event.target; btn.disabled=true;
    let done=0; const total=categories.filter(c=>c.url).length;
    for (let i=0;i<categories.length;i++) {
        if (categories[i].url) { btn.textContent=`⏳ ${++done}/${total}…`; await analyzePage(i,categories[i].url,false); await new Promise(r=>setTimeout(r,300)); }
    }
    btn.disabled=false; btn.textContent=`✅ ${done} analysiert!`;
    setTimeout(()=>btn.textContent='🔍 Alle analysieren',3000);
}

async function generateAll() {
    const btn=event.target; btn.disabled=true;
    let done=0;
    for (let i=0;i<categories.length;i++) {
        btn.textContent=`⏳ ${++done}/${categories.length}…`;
        await optimizeCategory(i);
        await new Promise(r=>setTimeout(r,800));
    }
    btn.disabled=false; btn.textContent=`✅ ${done} optimiert!`;
    setTimeout(()=>btn.textContent='✨ Alle optimieren',3000);
}

function updateCounter(idx, type, max) {
    const input = document.getElementById((type==='title'?'title':'desc')+'-'+idx);
    const len=input.value.length;
    const el=document.getElementById((type==='title'?'tc':'dc')+'-'+idx);
    const bar=document.getElementById((type==='title'?'tb':'db')+'-'+idx);
    el.textContent=len+'/'+max;
    el.className='char-hint '+(len>max?'over':len>max*0.85?'good':'');
    bar.style.width=Math.min(len/max*100,100)+'%';
    bar.style.background=len>max?'var(--danger)':len>max*0.85?'var(--success)':'var(--warning)';
}

function updateSeoPreview(idx) {
    const text=document.getElementById('seotext-'+idx).value;
    const preview=document.getElementById('preview-'+idx);
    if(preview) preview.innerHTML=text;
    const len=text.replace(/<[^>]*>/g,'').length;
    const el=document.getElementById('sc-'+idx);
    el.textContent='~'+len+' Zeichen';
    el.className='char-hint '+(len>=300&&len<=600?'good':'warn');
}

function showToast(msg) {
    const t=document.createElement('div');
    t.style.cssText='position:fixed;bottom:24px;right:24px;background:#111;border:1px solid rgba(124,58,237,.4);color:var(--al);padding:10px 18px;border-radius:8px;font-size:13px;z-index:999;';
    t.textContent=msg; document.body.appendChild(t); setTimeout(()=>t.remove(),3000);
}
</script>
@endpush

</x-layouts.app>

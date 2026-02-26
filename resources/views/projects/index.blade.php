<x-layouts.app title="Meine Projekte">

<div style="max-width:900px;">

    {{-- Header --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px;">
        <div>
            <h2 style="font-size:22px; font-weight:600; color:var(--text-1); margin-bottom:4px;">🌐 Meine Projekte</h2>
            <p style="font-size:14px; color:var(--text-2);">Verwalte deine Shopware-Shops für die SEO-Optimierung.</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary" style="text-decoration:none;">
            ＋ Neues Projekt
        </a>
    </div>

    {{-- Projects Grid --}}
    @if ($projects->isEmpty())
        <div style="text-align:center; padding:60px 20px; color:var(--text-2);">
            <div style="font-size:48px; margin-bottom:16px;">🌐</div>
            <div style="font-size:18px; font-weight:500; color:var(--text-1); margin-bottom:8px;">Noch keine Projekte</div>
            <div style="font-size:14px; margin-bottom:20px;">Verbinde deinen ersten Shopware-Shop.</div>
            <a href="{{ route('projects.create') }}" class="btn btn-primary" style="text-decoration:none;">
                ＋ Erstes Projekt anlegen
            </a>
        </div>
    @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:16px;">

            @foreach ($projects as $project)
            <div class="card" style="padding:20px; display:flex; flex-direction:column; gap:14px;">

                {{-- Header --}}
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:38px; height:38px; border-radius:10px; background:rgba(124,58,237,0.15); display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">🌐</div>
                    <div style="min-width:0;">
                        <div style="font-size:15px; font-weight:600; color:var(--text-1); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $project->name }}</div>
                        <div style="font-size:12px; color:var(--text-3); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $project->shopware_url }}</div>
                    </div>
                    <span class="badge {{ $project->is_active ? 'badge-green' : 'badge-gray' }}" style="margin-left:auto; flex-shrink:0;">
                        {{ $project->is_active ? 'Aktiv' : 'Inaktiv' }}
                    </span>
                </div>

                {{-- SEO Tool Links --}}
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    <a href="{{ route('seo.products', $project) }}"
                       style="font-size:11px; padding:4px 10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:6px; color:var(--text-2); text-decoration:none; white-space:nowrap;">
                        🏷️ Produkte
                    </a>
                    <a href="{{ route('seo.categories', $project) }}"
                       style="font-size:11px; padding:4px 10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:6px; color:var(--text-2); text-decoration:none; white-space:nowrap;">
                        📁 Kategorien
                    </a>
                    <a href="{{ route('seo.alttext', $project) }}"
                       style="font-size:11px; padding:4px 10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:6px; color:var(--text-2); text-decoration:none; white-space:nowrap;">
                        🖼️ Alt-Texte
                    </a>
                </div>

                {{-- Actions --}}
                <div style="display:flex; gap:8px; border-top:1px solid rgba(255,255,255,0.06); padding-top:12px;">
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary" style="flex:1; font-size:12px; padding:6px 12px; text-decoration:none; text-align:center;">
                        ✏️ Bearbeiten
                    </a>
                    <form action="{{ route('projects.destroy', $project) }}" method="POST"
                          onsubmit="return confirm('Projekt „{{ addslashes($project->name) }}" wirklich löschen?')"
                          style="flex:1;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width:100%; font-size:12px; padding:6px 12px;">
                            🗑️ Löschen
                        </button>
                    </form>
                </div>

            </div>
            @endforeach

            {{-- Add card --}}
            <a href="{{ route('projects.create') }}"
               style="text-decoration:none; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:10px; border:2px dashed rgba(255,255,255,0.1); border-radius:12px; padding:30px 20px; color:var(--text-3); transition:all .2s;"
               onmouseover="this.style.borderColor='rgba(124,58,237,0.4)'; this.style.color='var(--text-2)'"
               onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.color='var(--text-3)'">
                <span style="font-size:28px;">＋</span>
                <span style="font-size:13px; font-weight:500;">Projekt hinzufügen</span>
            </a>
        </div>
    @endif

</div>

</x-layouts.app>

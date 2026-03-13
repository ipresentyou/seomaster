<x-layouts.app title="Dashboard">

<style>
.welcome-banner {
    display: flex; align-items: center; gap: 18px;
    padding: 20px 24px;
    background: linear-gradient(135deg, rgba(124,58,237,0.1), rgba(147,51,234,0.04));
    border: 1px solid rgba(124,58,237,0.2);
    border-radius: 12px;
    margin-bottom: 28px;
    position: relative; overflow: hidden;
}
.welcome-banner::before {
    content: '';
    position: absolute; top:0; left:0; right:0; height:1px;
    background: linear-gradient(90deg, transparent, rgba(167,139,250,0.5), transparent);
}
.welcome-avatar {
    width: 48px; height: 48px;
    background: linear-gradient(135deg, var(--accent), #9333ea);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; font-weight: 700; flex-shrink: 0;
    box-shadow: 0 0 20px var(--accent-glow);
}

/* Quick stats */
.quick-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 28px;
}

.qs-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 10px;
    padding: 14px 16px;
    transition: border-color 0.2s, transform 0.2s;
}
.qs-card:hover { border-color: rgba(124,58,237,0.3); transform: translateY(-1px); }
.qs-value { font-size: 22px; font-weight: 700; letter-spacing: -0.03em; }
.qs-label { font-size: 11px; color: var(--text-3); margin-top: 3px; }

/* Projects grid */
.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.project-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    padding: 24px;
    text-decoration: none; color: inherit;
    display: block;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}
.project-card::before {
    content: '';
    position: absolute; top:0; left:0; right:0; height:2px;
    background: linear-gradient(90deg, var(--accent), #9333ea, var(--accent));
    opacity: 0; transition: opacity 0.3s;
}
.project-card:hover::before { opacity: 1; }
.project-card:hover {
    border-color: rgba(124,58,237,0.5);
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.4);
}

.project-header { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 20px; }
.project-icon {
    width: 52px; height: 52px; border-radius: 14px;
    background: linear-gradient(135deg, rgba(14,165,233,0.15), rgba(59,130,246,0.08));
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(14,165,233,0.2);
}
.project-name { 
    font-size: 18px; 
    font-weight: 600; 
    line-height: 1.3;
    color: var(--text-1);
}
.project-url  { 
    font-size: 13px; 
    color: var(--text-3); 
    margin-top: 4px; 
    font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
}

.project-tools {
    display: flex; gap: 8px; flex-wrap: wrap;
}
.tool-chip {
    display: flex; align-items: center; gap: 6px;
    padding: 8px 14px; border-radius: 10px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    font-size: 12px; color: var(--text-2);
    text-decoration: none;
    transition: all 0.2s;
    font-weight: 500;
}
.tool-chip:hover {
    background: rgba(124,58,237,0.15);
    border-color: rgba(124,58,237,0.4);
    color: var(--accent-light);
    transform: translateY(-1px);
}

/* Add project card */
.add-project-card {
    background: transparent;
    border: 2px dashed rgba(124,58,237,0.3);
    border-radius: 16px;
    padding: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column; 
    gap: 12px;
    text-decoration: none;
    transition: all 0.3s;
    min-height: 180px;
}
.add-project-card:hover {
    border-color: rgba(124,58,237,0.6);
    background: rgba(124,58,237,0.08);
    transform: translateY(-2px);
}

/* Activity feed */
.activity-item {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.03);
}
.activity-item:last-child { border-bottom: none; }
.activity-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--accent); margin-top: 5px;
    flex-shrink: 0;
    box-shadow: 0 0 6px var(--accent-glow);
}
.activity-text { font-size: 12px; color: var(--text-2); line-height: 1.5; flex: 1; }
.activity-time { font-size: 11px; color: var(--text-3); flex-shrink: 0; }
</style>

{{-- Welcome Banner --}}
<div class="welcome-banner">
    <div class="welcome-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
    <div style="flex:1;">
        <div style="font-size:18px; font-weight:600; letter-spacing:-0.02em;">
            Willkommen zurück, {{ explode(' ', auth()->user()->name)[0] }}! 👋
        </div>
        <div style="font-size:12px; color:var(--text-2); margin-top:3px;">
            @if(auth()->user()->hasActiveSubscription())
                @php $sub = auth()->user()->activeSubscription; @endphp
                Du bist auf dem <strong style="color:#4285f4">{{ $sub->plan->name }}</strong>-Plan.
                @if($sub->isOnTrial())
                    Testphase endet {{ $sub->trial_ends_at->locale("de")->diffForHumans() }}.
                    @php
                        $todayUsage = \App\Models\ApiUsage::getTodayUsage(auth()->id(), 'openai') + \App\Models\ApiUsage::getTodayUsage(auth()->id(), 'gemini');
                        $remaining = max(0, 10 - $todayUsage);
                    @endphp
                    @if($remaining > 0)
                        <div style="margin-top:4px; font-size:11px; color:var(--success);">
                            🤖 {{ $remaining }} von 10 KI-Calls heute verfügbar
                        </div>
                    @else
                        <div style="margin-top:4px; font-size:11px; color:var(--warning);">
                            ⚠️ Tägliches Limit erreicht (10/10)
                        </div>
                    @endif
                @endif
            @else
                <span style="color:var(--warning)">⚠️ Kein aktives Abo.</span>
                <a href="{{ route('subscription.index') }}" style="color:var(--accent-light); text-decoration:none;">→ Jetzt starten</a>
            @endif
        </div>
    </div>
    <a href="{{ route('projects.create') }}" class="btn btn-primary">+ Neues Projekt</a>
</div>

{{-- Quick Stats --}}
<div class="quick-stats">
    @php
        $user = auth()->user();
        $projectCount = $user->seoProjects()->count();
        $credCount    = $user->apiCredentials()->count();
        $totalLogs    = $user->activityLogs()->count();
        $tokensUsed   = $user->activityLogs()->sum('ai_tokens_used');
    @endphp
    <div class="qs-card">
        <div class="qs-value">{{ $projectCount }}</div>
        <div class="qs-label">🌐 Projekte</div>
    </div>
    <div class="qs-card">
        <div class="qs-value">{{ $credCount }}</div>
        <div class="qs-label">🔑 Credentials</div>
    </div>
    <div class="qs-card">
        <div class="qs-value">{{ number_format($totalLogs) }}</div>
        <div class="qs-label">⚡ Aktionen gesamt</div>
    </div>
    <div class="qs-card">
        <div class="qs-value" style="color:var(--accent-light)">{{ number_format($tokensUsed) }}</div>
        <div class="qs-label">🤖 AI Tokens verbraucht</div>
    </div>
</div>

{{-- Projects --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
    <div style="font-size:15px; font-weight:600;">Deine Projekte</div>
    <a href="{{ route('projects.index') }}" style="font-size:12px; color:var(--accent-light); text-decoration:none;">
        Alle anzeigen →
    </a>
</div>

<div class="projects-grid">
    @forelse(auth()->user()->seoProjects()->limit(5)->get() as $project)
        <div class="project-card">
            <div class="project-header">
                <div class="project-icon">🌐</div>
                <div style="flex:1; min-width:0;">
                    <div class="project-name">{{ $project->name }}</div>
                    <div class="project-url">{{ parse_url($project->shopware_url, PHP_URL_HOST) }}</div>
                </div>
                @if($project->is_active)
                    <span class="badge badge-green" style="font-size:10px;">aktiv</span>
                @else
                    <span class="badge badge-gray" style="font-size:10px;">inaktiv</span>
                @endif
            </div>
            <div class="project-tools">
                <a href="{{ route('seo.products', $project) }}" class="tool-chip">🏷️ Produkte</a>
                <a href="{{ route('seo.categories', $project) }}" class="tool-chip">📁 Kategorien</a>
                <a href="{{ route('seo.alttext', $project) }}" class="tool-chip">🖼️ Alt-Text</a>
            </div>
        </div>
    @empty
        <div style="grid-column:1/-1;">
            <div style="background:var(--card-bg); border:1px solid var(--card-border); border-radius:12px; padding:32px; text-align:center; color:var(--text-3);">
                <div style="font-size:36px; margin-bottom:10px;">🌐</div>
                <div style="font-size:14px; font-weight:500; color:var(--text-2); margin-bottom:4px;">Noch keine Projekte</div>
                <div style="font-size:12px;">Verbinde deinen ersten Shopware-Shop</div>
            </div>
        </div>
    @endforelse

    <a href="{{ route('projects.create') }}" class="add-project-card">
        <span style="font-size:28px; color:var(--text-3);">+</span>
        <span style="font-size:13px; font-weight:500; color:var(--text-3);">Projekt hinzufügen</span>
    </a>
</div>

{{-- Activity + Credentials columns --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

    {{-- Recent Activity --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">⚡ Letzte Aktivität</div>
        </div>
        <div class="card-body" style="padding:16px 20px;">
            @php $logs = auth()->user()->activityLogs()->with('project')->latest()->limit(8)->get(); @endphp
            @forelse($logs as $log)
                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <div class="activity-text">
                        <span style="color:var(--text-1)">{{ match(true) {
                            str_contains($log->action, 'alt_text.generated') => '🖼️ Alt-Text generiert',
                            str_contains($log->action, 'alt_text.saved')    => '💾 Alt-Text gespeichert',
                            str_contains($log->action, 'meta.saved')        => '💾 Meta gespeichert',
                            str_contains($log->action, 'seotext.saved')     => '💾 SEO-Text gespeichert',
                            default => $log->action,
                        } }}</span>
                        @if($log->project)
                            <span style="color:var(--text-3);"> · {{ $log->project->name }}</span>
                        @endif
                    </div>
                    <div class="activity-time">{{ $log->created_at->diffForHumans(short: true) }}</div>
                </div>
            @empty
                <div style="text-align:center; color:var(--text-3); padding:20px 0; font-size:12px;">
                    Noch keine Aktivitäten
                </div>
            @endforelse
        </div>
    </div>

    {{-- Credentials Status --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">🔑 Verbindungen</div>
            <a href="{{ route('credentials.create') }}" style="font-size:12px; color:var(--accent-light); text-decoration:none;">+ Hinzufügen</a>
        </div>
        <div class="card-body" style="padding:8px 0;">
            @php
                $credProviders = [
                    'shopware'              => ['🛒', 'Shopware'],
                    'openai'                => ['🤖', 'OpenAI'],
                    'gemini'                => ['✨', 'Gemini'],
                    'google_search_console' => ['📊', 'GSC'],
                ];
                $userCreds = auth()->user()->apiCredentials()->get()->groupBy('provider');
            @endphp
            @foreach($credProviders as $key => [$icon, $name])
                @php $creds = $userCreds->get($key, collect()); @endphp
                <div style="display:flex; align-items:center; gap:12px; padding:10px 20px; border-bottom:1px solid rgba(255,255,255,0.03);">
                    <span style="font-size:16px; width:22px; text-align:center;">{{ $icon }}</span>
                    <span style="flex:1; font-size:13px;">{{ $name }}</span>
                    @if($creds->isNotEmpty())
                        <span class="badge badge-green" style="font-size:10px;">
                            {{ $creds->count() }}x verbunden
                        </span>
                    @else
                        <a href="{{ route('credentials.create', ['provider' => $key]) }}"
                           style="font-size:11px; color:var(--text-3); text-decoration:none; border:1px dashed rgba(255,255,255,0.1); padding:2px 8px; border-radius:6px;">
                            Verbinden
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

</x-layouts.app>

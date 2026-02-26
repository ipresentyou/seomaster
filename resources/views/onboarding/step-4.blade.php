<x-layouts.onboarding :step="$step" :steps="$steps" :progress="100" :totalSteps="$totalSteps" title="Fertig!">

@php
    $sub        = $user->activeSubscription()->with('plan')->first();
    $hasProject = $user->seoProjects()->exists();
    $hasShop    = \App\Models\ApiCredential::where('user_id', $user->id)->where('is_active', true)->exists();
@endphp

<style>
/* ── Confetti burst (CSS only, subtile) ──────────────────────────────── */
@keyframes floatUp {
    0%   { transform: translateY(0) rotate(0deg);   opacity: 1; }
    100% { transform: translateY(-80px) rotate(20deg); opacity: 0; }
}
.confetti-dot {
    position: absolute;
    width: 8px; height: 8px;
    border-radius: 2px;
    animation: floatUp 1.4s ease-out forwards;
}

/* ── Checklist ───────────────────────────────────────────────────────── */
.check-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 14px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 10px;
    margin-bottom: 10px;
    font-size: 13px;
    color: var(--text-2);
    transition: border-color 0.2s;
}
.check-item.done {
    border-color: rgba(16,185,129,0.25);
    background: rgba(16,185,129,0.04);
    color: var(--text-1);
}
.check-dot {
    width: 28px; height: 28px; flex-shrink: 0;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
    background: rgba(255,255,255,0.06);
}
.check-item.done .check-dot {
    background: rgba(16,185,129,0.2);
}

/* ── Quick-start grid ────────────────────────────────────────────────── */
.qs-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 24px;
}
.qs-card {
    display: flex; align-items: center; gap: 12px;
    padding: 14px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 11px;
    text-decoration: none;
    color: var(--text-1);
    transition: all 0.2s;
    font-size: 13px;
    font-weight: 500;
}
.qs-card:hover {
    border-color: rgba(124,58,237,0.4);
    background: rgba(124,58,237,0.06);
    transform: translateY(-1px);
}
.qs-icon {
    font-size: 22px;
    flex-shrink: 0;
}
</style>

{{-- Confetti dots (CSS animation) --}}
<div style="position:relative; height:0; overflow:visible;">
    @foreach([[20,'#7c3aed',0],[80,'#9333ea',0.2],[50,'#a78bfa',0.4],[10,'#f472b6',0.1],[90,'#34d399',0.3]] as [$l,$c,$d])
        <div class="confetti-dot" style="left:{{$l}}%; background:{{$c}}; animation-delay:{{$d}}s;"></div>
    @endforeach
</div>

<div class="ob-step-badge">
    <span>🎉</span> Setup abgeschlossen
</div>

<h1 class="ob-title">
    Alles bereit,<br>
    <span style="background:linear-gradient(135deg,#34d399,#10b981);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
        {{ explode(' ', $user->name)[0] }}!
    </span>
</h1>

<p class="ob-subtitle">
    Dein SEOmaster-Konto ist einsatzbereit.
    Hier ist deine Setup-Zusammenfassung.
</p>

{{-- Checklist --}}
<div style="margin-bottom: 28px;">
    <div class="check-item done">
        <div class="check-dot">✓</div>
        <span>Konto erstellt & verifiziert</span>
    </div>

    <div class="check-item {{ $sub ? 'done' : '' }}">
        <div class="check-dot">{{ $sub ? '✓' : '—' }}</div>
        <span>
            @if($sub)
                <strong>{{ $sub->plan->name ?? '' }}-Plan</strong> aktiv
                @if($sub->isOnTrial()) — Trial bis {{ $sub->trial_ends_at?->format('d.m.Y') }} @endif
            @else
                Noch kein Plan gewählt
            @endif
        </span>
        @if(!$sub)
            <a href="{{ route('subscription.index') }}"
               style="margin-left:auto; font-size:11px; color:var(--accent-l); text-decoration:none;">
                Plan wählen →
            </a>
        @endif
    </div>

    <div class="check-item {{ $hasShop ? 'done' : '' }}">
        <div class="check-dot">{{ $hasShop ? '✓' : '—' }}</div>
        <span>
            @if($hasShop)
                Shop verbunden
            @else
                Shop noch nicht verbunden
            @endif
        </span>
        @if(!$hasShop)
            <a href="{{ route('credentials.index') }}"
               style="margin-left:auto; font-size:11px; color:var(--accent-l); text-decoration:none;">
                Jetzt verbinden →
            </a>
        @endif
    </div>

    <div class="check-item {{ $hasProject ? 'done' : '' }}">
        <div class="check-dot">{{ $hasProject ? '✓' : '—' }}</div>
        <span>
            @if($hasProject)
                Erstes Projekt angelegt
            @else
                Noch kein Projekt
            @endif
        </span>
        @if(!$hasProject)
            <a href="{{ route('projects.create') }}"
               style="margin-left:auto; font-size:11px; color:var(--accent-l); text-decoration:none;">
                Erstellen →
            </a>
        @endif
    </div>
</div>

{{-- Quick-start --}}
<div style="font-size:12px; font-weight:600; color:var(--text-3);
            letter-spacing:0.05em; text-transform:uppercase; margin-bottom:12px;">
    Schnellstart
</div>

<div class="qs-grid">
    <a href="{{ route('seo.products') }}" class="qs-card">
        <div class="qs-icon">🏷️</div>
        <div>
            <div>Produkt-SEO</div>
            <div style="font-size:11px;color:var(--text-3);font-weight:400;">Titel & Beschreibungen</div>
        </div>
    </a>
    <a href="{{ route('seo.categories') }}" class="qs-card">
        <div class="qs-icon">📁</div>
        <div>
            <div>Kategorie-SEO</div>
            <div style="font-size:11px;color:var(--text-3);font-weight:400;">Keywords & Meta</div>
        </div>
    </a>
    <a href="{{ route('seo.alttext') }}" class="qs-card">
        <div class="qs-icon">🖼️</div>
        <div>
            <div>Alt-Texte</div>
            <div style="font-size:11px;color:var(--text-3);font-weight:400;">KI-generiert</div>
        </div>
    </a>
    <a href="{{ route('projects.index') }}" class="qs-card">
        <div class="qs-icon">📋</div>
        <div>
            <div>Projekte</div>
            <div style="font-size:11px;color:var(--text-3);font-weight:400;">Übersicht</div>
        </div>
    </a>
</div>

<form method="POST" action="{{ route('onboarding.complete') }}">
    @csrf
    <button type="submit" class="btn-next" style="
        background: linear-gradient(135deg, #059669, #10b981);
        box-shadow: 0 0 24px rgba(16,185,129,0.3);
    ">
        🚀 Zum Dashboard →
    </button>
</form>

</x-layouts.onboarding>

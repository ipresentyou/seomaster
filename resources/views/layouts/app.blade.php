<!DOCTYPE html>
<html lang="de" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SEOmaster' }} – SEO Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    /* ── Base ───────────────────────────────────────────────── */
    :root {
        --sidebar-bg:   #08080f;
        --main-bg:      #0c0c14;
        --card-bg:      #111119;
        --card-border:  rgba(139,92,246,0.15);
        --accent:       #7c3aed;
        --accent-light: #a78bfa;
        --accent-glow:  rgba(124,58,237,0.15);
        --text-1:       #f0eeff;
        --text-2:       #9d9bbf;
        --text-3:       #4e4c6a;
        --success:      #10b981;
        --warning:      #f59e0b;
        --danger:       #f43f5e;
        --info:         #0ea5e9;
        --sidebar-w:    240px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Inter', system-ui, sans-serif;
        background: var(--main-bg);
        color: var(--text-1);
        display: flex;
        min-height: 100vh;
        font-size: 14px;
        -webkit-font-smoothing: antialiased;
    }

    /* ── Sidebar ─────────────────────────────────────────────── */
    .sidebar {
        width: var(--sidebar-w);
        flex-shrink: 0;
        background: var(--sidebar-bg);
        border-right: 1px solid rgba(139,92,246,0.1);
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0; left: 0; bottom: 0;
        z-index: 50;
    }

    .sidebar-logo {
        padding: 20px 16px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .logo-icon {
        width: 32px; height: 32px;
        background: linear-gradient(135deg, var(--accent), #9333ea);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        box-shadow: 0 0 20px var(--accent-glow);
        flex-shrink: 0;
    }

    .logo-text {
        font-size: 16px; font-weight: 700;
        letter-spacing: -0.03em;
        background: linear-gradient(135deg, #f0eeff, #a78bfa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .nav-section { padding: 14px 8px 4px; }

    .nav-label {
        padding: 0 10px 5px;
        font-size: 10px;
        letter-spacing: 0.1em; text-transform: uppercase;
        color: var(--text-3);
    }

    .nav-item {
        display: flex; align-items: center; gap: 9px;
        padding: 8px 10px;
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-2);
        font-size: 13px; font-weight: 450;
        transition: all 0.15s;
        margin: 1px 0;
        border: 1px solid transparent;
    }

    .nav-item:hover { background: rgba(139,92,246,0.06); color: var(--text-1); }

    .nav-item.active {
        background: rgba(124,58,237,0.15);
        color: var(--accent-light);
        border-color: rgba(124,58,237,0.3);
    }

    .nav-badge {
        margin-left: auto;
        background: rgba(124,58,237,0.2);
        color: var(--accent-light);
        font-size: 10px;
        padding: 1px 6px;
        border-radius: 999px;
    }

    .sidebar-footer {
        margin-top: auto;
        padding: 10px 8px;
        border-top: 1px solid rgba(255,255,255,0.05);
    }

    .user-chip {
        display: flex; align-items: center; gap: 9px;
        padding: 8px 10px;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s;
    }
    .user-chip:hover { background: rgba(255,255,255,0.04); }

    .user-avatar {
        width: 30px; height: 30px;
        background: linear-gradient(135deg, var(--accent), #9333ea);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
    }

    .user-name  { font-size: 12px; font-weight: 500; color: var(--text-1); }
    .user-email { font-size: 11px; color: var(--text-3); }

    /* Subscription badge in sidebar */
    .sub-chip {
        display: flex; align-items: center; gap: 6px;
        margin: 0 8px 10px;
        padding: 8px 12px;
        background: rgba(124,58,237,0.08);
        border: 1px solid rgba(124,58,237,0.2);
        border-radius: 8px;
        font-size: 11px; color: var(--accent-light);
    }

    /* ── Main Content ─────────────────────────────────────────── */
    .main-wrapper {
        margin-left: var(--sidebar-w);
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .topbar {
        height: 52px;
        background: rgba(8,8,15,0.8);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(139,92,246,0.08);
        display: flex; align-items: center;
        padding: 0 28px;
        position: sticky; top: 0; z-index: 40;
        gap: 12px;
    }

    .topbar-breadcrumb {
        font-size: 12px; color: var(--text-3);
        display: flex; align-items: center; gap: 6px;
    }
    .topbar-breadcrumb .sep { color: var(--text-3); }
    .topbar-breadcrumb .current { color: var(--text-2); }

    .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 10px; }

    .icon-btn {
        width: 32px; height: 32px;
        border-radius: 8px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 15px;
        transition: background 0.15s;
        text-decoration: none;
    }
    .icon-btn:hover { background: rgba(255,255,255,0.08); }

    .main-content {
        padding: 28px;
        flex: 1;
        max-width: 1200px;
        width: 100%;
    }

    .page-header {
        display: flex; align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 28px;
        gap: 16px;
    }

    .page-title {
        font-size: 22px;
        letter-spacing: -0.03em;
    }

    .page-subtitle { font-size: 13px; color: var(--text-3); margin-top: 3px; }

    /* ── Buttons ──────────────────────────────────────────────── */
    .btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        cursor: pointer; border: none;
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
        box-shadow: 0 0 20px var(--accent-glow);
    }
    .btn-primary:hover { background: #6d28d9; transform: translateY(-1px); }

    .btn-secondary {
        background: rgba(255,255,255,0.06);
        color: var(--text-2);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .btn-secondary:hover { background: rgba(255,255,255,0.09); color: var(--text-1); }

    .btn-danger {
        background: rgba(244,63,94,0.12);
        color: #fb7185;
        border: 1px solid rgba(244,63,94,0.3);
    }
    .btn-danger:hover { background: rgba(244,63,94,0.2); }

    .btn-sm { padding: 5px 12px; font-size: 12px; }

    /* ── Cards ────────────────────────────────────────────────── */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
    }

    .card-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        display: flex; align-items: center; justify-content: space-between;
    }

    .card-title { font-size: 14px; }
    .card-subtitle { font-size: 12px; color: var(--text-3); margin-top: 2px; }
    .card-body { padding: 20px; }

    /* ── Alerts ───────────────────────────────────────────────── */
    .alert {
        padding: 12px 16px; border-radius: 8px;
        font-size: 13px;
        display: flex; align-items: flex-start; gap: 10px;
        margin-bottom: 20px;
    }
    .alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #34d399; }
    .alert-warning { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); color: #fbbf24; }
    .alert-danger   { background: rgba(244,63,94,0.1);  border: 1px solid rgba(244,63,94,0.3);  color: #fb7185; }
    .alert-info     { background: rgba(124,58,237,0.08); border: 1px solid rgba(124,58,237,0.25); color: var(--accent-light); }

    /* ── Badges ───────────────────────────────────────────────── */
    .badge {
        display: inline-flex; align-items: center;
        padding: 2px 9px; border-radius: 999px;
        font-size: 11px; font-weight: 500;
    }
    .badge-violet { background: rgba(124,58,237,0.15); color: var(--accent-light); border: 1px solid rgba(124,58,237,0.3); }
    .badge-green  { background: rgba(16,185,129,0.12); color: #34d399;             border: 1px solid rgba(16,185,129,0.3); }
    .badge-amber  { background: rgba(245,158,11,0.12); color: #fbbf24;             border: 1px solid rgba(245,158,11,0.3); }
    .badge-red    { background: rgba(244,63,94,0.12);  color: #fb7185;             border: 1px solid rgba(244,63,94,0.3); }
    .badge-gray   { background: rgba(255,255,255,0.06); color: var(--text-2);       border: 1px solid rgba(255,255,255,0.1); }

    /* ── Forms ────────────────────────────────────────────────── */
    .form-group { margin-bottom: 18px; }

    .form-label {
        display: block;
        font-size: 12px; font-weight: 500;
        color: var(--text-2);
        margin-bottom: 6px;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 9px 12px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 8px;
        color: var(--text-1);
        font-size: 13px;
        font-family: inherit;
        transition: all 0.2s;
        outline: none;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-glow);
        background: rgba(124,58,237,0.05);
    }

    .form-input::placeholder { color: var(--text-3); }

    .form-hint { font-size: 11px; color: var(--text-3); margin-top: 5px; }

    .form-error {
        font-size: 11px; color: #fb7185;
        margin-top: 5px;
        display: flex; align-items: center; gap: 4px;
    }

    /* ── Scrollbar ────────────────────────────────────────────── */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(124,58,237,0.3); border-radius: 999px; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── Sidebar ──────────────────────────────────────────────── --}}
<aside class="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('images/logo_seomaster.svg') }}" width="240" alt="Logo">
    </a>

    <div class="nav-section">
        <div class="nav-label">Übersicht</div>
        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span>⬛</span> Dashboard
        </a>
    </div>

    <div class="nav-section">
        <div class="nav-label">SEO Tools</div>
        <a href="{{ route('projects.index') }}"
           class="nav-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
            <span>🌐</span> Meine Projekte
            @if(auth()->user()->seoProjects()->count() > 0)
                <span class="nav-badge">{{ auth()->user()->seoProjects()->count() }}</span>
            @endif
        </a>
    </div>

    <div class="nav-section">
        <div class="nav-label">Konto</div>
        <a href="{{ route('credentials.index') }}"
           class="nav-item {{ request()->routeIs('credentials.*') ? 'active' : '' }}">
            <span>🔑</span> API Credentials
        </a>
        <a href="{{ route('subscription.index') }}"
           class="nav-item {{ request()->routeIs('subscription.*') ? 'active' : '' }}">
            <span>💳</span> Abonnement
        </a>
    </div>

    {{-- Subscription Badge --}}
    @if(auth()->user()->hasActiveSubscription())
        @php $sub = auth()->user()->activeSubscription; @endphp
        <div class="sub-chip">
            <span>✨</span>
            <span>{{ $sub->plan->name ?? 'Plan' }}
                @if($sub->isOnTrial())
                    <span style="color:var(--warning)">· Trial</span>
                @endif
            </span>
        </div>
    @else
        <a href="{{ route('subscription.index') }}" style="margin: 0 8px 10px; display:flex; align-items:center; gap:6px; padding:8px 12px; background:rgba(244,63,94,0.08); border:1px solid rgba(244,63,94,0.2); border-radius:8px; font-size:11px; color:#fb7185; text-decoration:none;">
            <span>⚠️</span> Kein aktives Abo
        </a>
    @endif

    <div class="sidebar-footer">
        <a href="{{ route('profile.edit') }}" class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ Str::limit(auth()->user()->email, 22) }}</div>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:4px;">
            @csrf
            <button type="submit" class="nav-item" style="width:100%; cursor:pointer; background:none; border:none; text-align:left;">
                <span>🚪</span> <span style="color:var(--text-3)">Abmelden</span>
            </button>
        </form>
    </div>
</aside>

{{-- ── Main ───────────────────────────────────────────────────── --}}
<div class="main-wrapper">
    {{-- Topbar --}}
    <div class="topbar">
        <div class="topbar-breadcrumb">
            <span>SEOmaster</span>
            <span class="sep">›</span>
            <span class="current">{{ $title ?? 'Dashboard' }}</span>
        </div>
        <div class="topbar-right">
            @if(auth()->user()->hasRole('admin'))
                <a href="/admin" class="icon-btn" title="Admin Panel">🛡</a>
            @endif
            <a href="{{ route('subscription.index') }}" class="icon-btn" title="Abo">💳</a>
        </div>
    </div>

    {{-- Flash Messages --}}
    <div style="padding: 0 28px;">
        @if(session('success'))
            <div class="alert alert-success" style="margin-top:16px;">✅ {{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning" style="margin-top:16px;">⚠️ {{ session('warning') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" style="margin-top:16px;">❌ {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" style="margin-top:16px; flex-direction:column; align-items:flex-start;">
                <strong>❌ Fehler:</strong>
                <ul style="margin-top:6px; padding-left:16px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Page Content --}}
    <div class="main-content">
        {{ $slot }}
    </div>
</div>

@stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SEOmaster' }} – SEO Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    /* ── Base ───────────────────────────────────────────────── */
    :root {
        --sidebar-bg:   #fff;
        --main-bg:      #f8f9fa;
        --card-bg:      #fff;
        --card-border:  #dadce0;
        --accent:       #1a73e8;
        --accent-hover: #1765cc;
        --accent-light: #e8f0fe;
        --text-1:       #202124;
        --text-2:       #5f6368;
        --text-3:       #80868b;
        --success:      #1e8e3e;
        --warning:      #e37400;
        --danger:       #d93025;
        --info:         #1a73e8;
        --border:       #dadce0;
        --sidebar-w:    240px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Roboto', arial, sans-serif;
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
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0; left: 0; bottom: 0;
        z-index: 50;
    }

    .sidebar-logo {
        padding: 16px 16px 14px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .logo-icon {
        width: 32px; height: 32px;
        background: var(--accent);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .logo-text {
        font-family: 'Google Sans', sans-serif;
        font-size: 16px; font-weight: 500;
        color: var(--text-1);
        letter-spacing: -0.01em;
    }

    .nav-section { padding: 10px 8px 4px; }

    .nav-label {
        padding: 4px 10px 4px;
        font-size: 11px; font-weight: 500;
        letter-spacing: 0.07em; text-transform: uppercase;
        color: var(--text-3);
    }

    .nav-item {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 10px;
        border-radius: 0 24px 24px 0;
        text-decoration: none;
        color: var(--text-2);
        font-size: 13px; font-weight: 400;
        transition: all 0.15s;
        margin: 1px 0;
        margin-right: 8px;
    }

    .nav-item:hover { background: #f1f3f4; color: var(--text-1); }

    .nav-item.active {
        background: var(--accent-light);
        color: var(--accent);
        font-weight: 500;
    }

    .nav-badge {
        margin-left: auto;
        background: var(--accent-light);
        color: var(--accent);
        font-size: 11px; font-weight: 500;
        padding: 1px 7px;
        border-radius: 999px;
    }

    .sidebar-footer {
        margin-top: auto;
        padding: 8px 8px;
        border-top: 1px solid var(--border);
    }

    .user-chip {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 10px;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s;
    }
    .user-chip:hover { background: #f1f3f4; }

    .user-avatar {
        width: 30px; height: 30px;
        background: var(--accent);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 500;
        color: #fff;
        flex-shrink: 0;
    }

    .user-name  { font-size: 13px; font-weight: 500; color: var(--text-1); }
    .user-email { font-size: 11px; color: var(--text-3); }

    /* Subscription badge in sidebar */
    .sub-chip {
        display: flex; align-items: center; gap: 6px;
        margin: 0 8px 8px;
        padding: 7px 12px;
        background: var(--accent-light);
        border-radius: 8px;
        font-size: 12px; color: var(--accent);
        font-weight: 500;
    }

    .sub-chip-warn {
        display: flex; align-items: center; gap: 6px;
        margin: 0 8px 8px;
        padding: 7px 12px;
        background: #fce8e6;
        border-radius: 8px;
        font-size: 12px; color: var(--danger);
        font-weight: 500;
        text-decoration: none;
    }
    .sub-chip-warn:hover { background: #fad2cf; }

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
        background: #fff;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center;
        padding: 0 28px;
        position: sticky; top: 0; z-index: 40;
        gap: 12px;
    }

    .topbar-breadcrumb {
        font-size: 13px; color: var(--text-2);
        display: flex; align-items: center; gap: 6px;
        font-family: 'Google Sans', sans-serif;
    }
    .topbar-breadcrumb .sep { color: var(--text-3); }
    .topbar-breadcrumb .current { color: var(--text-1); font-weight: 500; }

    .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }

    .icon-btn {
        width: 34px; height: 34px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 16px;
        transition: background 0.15s;
        text-decoration: none;
    }
    .icon-btn:hover { background: #f1f3f4; }

    .main-content {
        padding: 28px;
        flex: 1;
        max-width: 1200px;
        width: 100%;
    }

    .page-header {
        display: flex; align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 24px;
        gap: 16px;
    }

    .page-title {
        font-family: 'Google Sans', sans-serif;
        font-size: 22px; font-weight: 400;
        color: var(--text-1);
        letter-spacing: -0.01em;
    }

    .page-subtitle { font-size: 13px; color: var(--text-3); margin-top: 4px; }

    /* ── Buttons ──────────────────────────────────────────────── */
    .btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 8px 20px;
        border-radius: 4px;
        font-size: 14px; font-weight: 500;
        cursor: pointer; border: none;
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
        font-family: 'Google Sans', sans-serif;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
        box-shadow: 0 1px 2px rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
    }
    .btn-primary:hover {
        background: var(--accent-hover);
        box-shadow: 0 1px 3px rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
    }

    .btn-secondary {
        background: #fff;
        color: var(--accent);
        border: 1px solid var(--border);
        box-shadow: 0 1px 2px rgba(60,64,67,.3);
    }
    .btn-secondary:hover {
        background: var(--accent-light);
        border-color: #d2e3fc;
    }

    .btn-danger {
        background: var(--danger);
        color: #fff;
    }
    .btn-danger:hover { background: #c5221f; }

    .btn-sm { padding: 5px 14px; font-size: 13px; }

    /* ── Cards ────────────────────────────────────────────────── */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(60,64,67,.1);
    }

    .card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
    }

    .card-title {
        font-family: 'Google Sans', sans-serif;
        font-size: 14px; font-weight: 500;
        color: var(--text-1);
    }
    .card-subtitle { font-size: 12px; color: var(--text-3); margin-top: 2px; }
    .card-body { padding: 20px; }

    /* ── Alerts ───────────────────────────────────────────────── */
    .alert {
        padding: 12px 16px; border-radius: 4px;
        font-size: 13px;
        display: flex; align-items: flex-start; gap: 10px;
        margin-bottom: 20px;
    }
    .alert-success { background: #e6f4ea; border-left: 4px solid var(--success); color: #137333; }
    .alert-warning { background: #fef7e0; border-left: 4px solid var(--warning); color: #b06000; }
    .alert-danger   { background: #fce8e6; border-left: 4px solid var(--danger);  color: #c5221f; }
    .alert-info     { background: var(--accent-light); border-left: 4px solid var(--accent); color: #174ea6; }

    /* ── Badges ───────────────────────────────────────────────── */
    .badge {
        display: inline-flex; align-items: center;
        padding: 2px 10px; border-radius: 999px;
        font-size: 11px; font-weight: 500;
    }
    .badge-blue   { background: var(--accent-light); color: var(--accent); }
    .badge-green  { background: #e6f4ea; color: var(--success); }
    .badge-amber  { background: #fef7e0; color: var(--warning); }
    .badge-red    { background: #fce8e6; color: var(--danger); }
    .badge-gray   { background: #f1f3f4; color: var(--text-2); }

    /* backwards compat */
    .badge-violet { background: var(--accent-light); color: var(--accent); }

    /* ── Forms ────────────────────────────────────────────────── */
    .form-group { margin-bottom: 18px; }

    .form-label {
        display: block;
        font-size: 13px; font-weight: 500;
        color: var(--text-1);
        margin-bottom: 6px;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 9px 12px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 4px;
        color: var(--text-1);
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s;
        outline: none;
    }

    .form-input:hover, .form-select:hover, .form-textarea:hover {
        border-color: #b8bdc3;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 2px rgba(26,115,232,0.15);
    }

    .form-input::placeholder { color: var(--text-3); }
    .form-hint { font-size: 12px; color: var(--text-3); margin-top: 5px; }

    .form-error {
        font-size: 12px; color: var(--danger);
        margin-top: 5px;
    }

    /* ── Table ────────────────────────────────────────────────── */
    table { width: 100%; border-collapse: collapse; }
    th {
        font-size: 12px; font-weight: 500;
        color: var(--text-2);
        text-align: left;
        padding: 10px 16px;
        border-bottom: 1px solid var(--border);
        background: #f8f9fa;
    }
    td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f3f4;
        font-size: 13px;
        color: var(--text-1);
    }
    tr:hover td { background: #f8f9fa; }

    /* ── Scrollbar ────────────────────────────────────────────── */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #dadce0; border-radius: 999px; }
    ::-webkit-scrollbar-thumb:hover { background: #bdc1c6; }
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
            <span>📊</span> Dashboard
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
        <a href="{{ route('profile.edit') }}"
           class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span>👤</span> Profil
        </a>
    </div>

    {{-- Subscription Badge --}}
    @if(auth()->user()->hasActiveSubscription())
        @php $sub = auth()->user()->activeSubscription; @endphp
        <div class="sub-chip">
            <span>✨</span>
            <span>{{ $sub->plan->name ?? 'Plan' }}
                @if($sub->isOnTrial())
                    · <span style="color:var(--warning)">Trial</span>
                @endif
            </span>
        </div>
    @else
        <a href="{{ route('subscription.index') }}" class="sub-chip-warn">
            <span>⚠️</span> Kein aktives Abo
        </a>
    @endif

    <div class="sidebar-footer">
        <a href="{{ route('profile.edit') }}" class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ Str::limit(auth()->user()->email, 24) }}</div>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:4px;">
            @csrf
            <button type="submit" class="nav-item" style="width:100%; cursor:pointer; background:none; border:none; text-align:left; color:var(--text-3); font-size:13px;">
                <span>🚪</span> Abmelden
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
        @if(session('info'))
            <div class="alert alert-info" style="margin-top:16px;">ℹ️ {{ session('info') }}</div>
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

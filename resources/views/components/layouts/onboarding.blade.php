<!DOCTYPE html>
<html lang="de" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Einrichtung' }} — SEOmaster</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    :root {
        --bg:           #f8f9fa;
        --panel-bg:     #ffffff;
        --card-bg:      #ffffff;
        --border:       #dadce0;
        --accent:       #1a73e8;
        --accent-l:     #1a73e8;
        --accent-glow:  rgba(26,115,232,0.2);
        --text-1:       #202124;
        --text-2:       #5f6368;
        --text-3:       #9aa0a6;
        --success:      #1e8e3e;
        --warning:      #f29900;
        --danger:       #d93025;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Inter', system-ui, sans-serif;
        background: var(--bg);
        color: var(--text-1);
        min-height: 100vh;
        display: flex;
        -webkit-font-smoothing: antialiased;
    }

    /* ── Background effects ──────────────────────────────────────────── */
    body::before {
        content: '';
        position: fixed; inset: 0; pointer-events: none;
        background:
            radial-gradient(ellipse 70% 60% at 30% 10%, rgba(124,58,237,0.10) 0%, transparent 60%),
            radial-gradient(ellipse 40% 40% at 80% 90%, rgba(147,51,234,0.06) 0%, transparent 60%);
    }

    /* ── Left: Steps sidebar ─────────────────────────────────────────── */
    .ob-sidebar {
        width: 280px;
        flex-shrink: 0;
        background: var(--panel-bg);
        border-right: 1px solid var(--border);
        padding: 40px 32px;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 1;
    }

    .ob-logo {
        display: flex; align-items: center; gap: 10px;
        text-decoration: none; margin-bottom: 48px;
    }
    .ob-logo-icon {
        width: 36px; height: 36px;
        background: linear-gradient(135deg, var(--accent), #9333ea);
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        box-shadow: 0 0 20px var(--accent-glow);
    }
    .ob-logo-name {
        font-family: 'Roboto', Arial, sans-serif;
        font-size: 18px; font-weight: 800;
        background: linear-gradient(135deg, #f0eeff, #a78bfa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Step list */
    .ob-steps { flex: 1; }

    .ob-step-item {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 0;
        position: relative;
    }
    /* Connector line */
    .ob-step-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 15px; top: 38px;
        width: 1px; height: calc(100% - 12px);
        background: var(--border);
    }

    .ob-step-dot {
        width: 32px; height: 32px; flex-shrink: 0;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 600;
        border: 1.5px solid var(--border);
        background: rgba(255,255,255,0.03);
        color: var(--text-3);
        transition: all 0.3s;
        position: relative; z-index: 1;
    }

    .ob-step-item.active .ob-step-dot {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
        box-shadow: 0 0 20px rgba(124,58,237,0.4);
    }

    .ob-step-item.done .ob-step-dot {
        background: rgba(16,185,129,0.15);
        border-color: rgba(16,185,129,0.4);
        color: var(--success);
    }

    .ob-step-label {
        font-size: 13px;
        color: var(--text-3);
        font-weight: 400;
        transition: color 0.2s;
    }
    .ob-step-item.active .ob-step-label {
        color: var(--text-1);
        font-weight: 500;
    }
    .ob-step-item.done .ob-step-label {
        color: var(--success);
    }

    /* Progress bar */
    .ob-progress-wrap {
        margin-top: auto;
        padding-top: 32px;
        border-top: 1px solid var(--border);
    }
    .ob-progress-meta {
        display: flex; justify-content: space-between;
        font-size: 11px; color: var(--text-3);
        margin-bottom: 8px;
    }
    .ob-progress-bar {
        height: 4px;
        background: rgba(255,255,255,0.07);
        border-radius: 999px;
        overflow: hidden;
    }
    .ob-progress-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--accent), #9333ea);
        transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 8px var(--accent-glow);
    }

    /* Skip link */
    .ob-skip-link {
        display: block;
        margin-top: 16px;
        text-align: center;
        font-size: 12px;
        color: var(--text-3);
        text-decoration: none;
        transition: color 0.2s;
    }
    .ob-skip-link:hover { color: var(--text-2); }

    /* ── Right: Content area ─────────────────────────────────────────── */
    .ob-main {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        position: relative; z-index: 1;
    }

    .ob-card {
        width: 100%;
        max-width: 560px;
    }

    .ob-step-badge {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 11px; font-weight: 600;
        color: var(--accent-l);
        background: rgba(124,58,237,0.1);
        border: 1px solid rgba(124,58,237,0.2);
        border-radius: 999px;
        padding: 3px 12px;
        margin-bottom: 20px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .ob-title {
        font-family: 'Roboto', Arial, sans-serif;
        font-size: 28px; font-weight: 700;
        letter-spacing: -0.03em;
        line-height: 1.2;
        margin-bottom: 10px;
    }

    .ob-subtitle {
        font-size: 14px;
        color: var(--text-2);
        line-height: 1.6;
        margin-bottom: 32px;
    }

    /* ── Form elements ───────────────────────────────────────────────── */
    .form-group { margin-bottom: 20px; }

    .form-label {
        display: block;
        font-size: 12px; font-weight: 500;
        color: var(--text-2);
        margin-bottom: 7px;
        letter-spacing: 0.02em;
    }

    .form-input, .form-select {
        width: 100%;
        padding: 11px 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        color: var(--text-1);
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s;
        outline: none;
    }
    .form-input:hover, .form-select:hover { border-color: rgba(124,58,237,0.3); }
    .form-input:focus, .form-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,0.12);
        background: rgba(124,58,237,0.04);
    }
    .form-input::placeholder { color: var(--text-3); }
    .form-input.error { border-color: rgba(244,63,94,0.5); }
    .form-input.error:focus { box-shadow: 0 0 0 3px rgba(244,63,94,0.1); }

    .form-select option { background: #1a1a2e; }

    .form-error {
        font-size: 11px; color: #fb7185;
        margin-top: 5px;
        display: flex; align-items: center; gap: 4px;
    }

    .form-hint {
        font-size: 11px; color: var(--text-3);
        margin-top: 5px; line-height: 1.5;
    }

    /* ── Buttons ─────────────────────────────────────────────────────── */
    .btn-next {
        width: 100%;
        padding: 12px 24px;
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 14px; font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 0 24px rgba(124,58,237,0.3);
        letter-spacing: 0.01em;
        margin-top: 8px;
    }
    .btn-next:hover {
        background: #6d28d9;
        transform: translateY(-1px);
        box-shadow: 0 4px 30px rgba(124,58,237,0.45);
    }
    .btn-next:active { transform: none; }

    .btn-skip-step {
        width: 100%;
        padding: 11px;
        background: transparent;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 10px;
        color: var(--text-3);
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 8px;
        font-family: inherit;
    }
    .btn-skip-step:hover {
        background: rgba(255,255,255,0.04);
        color: var(--text-2);
        border-color: rgba(255,255,255,0.12);
    }

    /* ── Alert ───────────────────────────────────────────────────────── */
    .alert {
        padding: 12px 16px;
        border-radius: 9px;
        font-size: 13px;
        margin-bottom: 20px;
        line-height: 1.5;
    }
    .alert-danger  { background: rgba(244,63,94,0.08);  border: 1px solid rgba(244,63,94,0.25);  color: #fb7185; }
    .alert-success { background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.25); color: #34d399; }
    .alert-info    { background: rgba(124,58,237,0.08); border: 1px solid rgba(124,58,237,0.25); color: var(--accent-l); }

    /* ── Responsive ──────────────────────────────────────────────────── */
    @media (max-width: 768px) {
        body { flex-direction: column; }
        .ob-sidebar { width: 100%; flex-direction: row; padding: 20px 24px; align-items: center; }
        .ob-logo { margin-bottom: 0; }
        .ob-steps { display: none; }
        .ob-progress-wrap { margin-top: 0; padding-top: 0; border-top: none; flex: 1; margin-left: 20px; }
        .ob-skip-link { display: none; }
        .ob-main { padding: 24px; align-items: flex-start; }
    }
    </style>
</head>
<body>
    {{-- ── Sidebar ───────────────────────────────────────────────────────── --}}
    <aside class="ob-sidebar">
        <a href="{{ route('dashboard') }}" class="ob-logo">
            <img src="{{ asset('images/logo_seomaster.svg') }}" width="240" alt="Logo">

        </a>

        <div class="ob-steps">
            @foreach($steps as $n => $s)
                @php
                    $isDone   = $n < $step;
                    $isActive = $n === $step;
                @endphp
                <div class="ob-step-item {{ $isActive ? 'active' : ($isDone ? 'done' : '') }}">
                    <div class="ob-step-dot">
                        @if($isDone)
                            ✓
                        @else
                            {{ $n }}
                        @endif
                    </div>
                    <span class="ob-step-label">{{ $s['label'] }}</span>
                </div>
            @endforeach
        </div>

        <div class="ob-progress-wrap">
            <div class="ob-progress-meta">
                <span>Fortschritt</span>
                <span>{{ $progress }}%</span>
            </div>
            <div class="ob-progress-bar">
                <div class="ob-progress-fill" style="width: {{ $progress }}%;"></div>
            </div>

            <a href="{{ route('onboarding.skip') }}" class="ob-skip-link"
               onclick="return confirm('Wizard überspringen? Du kannst ihn später in den Einstellungen nachholen.')">
                Überspringen →
            </a>
        </div>
    </aside>

    {{-- ── Main Content ───────────────────────────────────────────────────── --}}
    <main class="ob-main">
        <div class="ob-card">

            {{-- Flash alerts --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $err)
                        <div>{{ $err }}</div>
                    @endforeach
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</body>
</html>

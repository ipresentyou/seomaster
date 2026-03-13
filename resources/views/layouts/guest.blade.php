<!DOCTYPE html>
<html lang="de" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SEOmaster' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    :root {
        --bg:           #07070f;
        --card-bg:      #0e0e1a;
        --card-border:  rgba(124,58,237,0.2);
        --accent:       #7c3aed;
        --accent-light: #a78bfa;
        --accent-glow:  rgba(124,58,237,0.2);
        --text-1:       #f0eeff;
        --text-2:       #9d9bbf;
        --text-3:       #3d3b5a;
        --success:      #10b981;
        --danger:       #f43f5e;
        --warning:      #f59e0b;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Inter', system-ui, sans-serif;
        background: var(--bg);
        color: var(--text-1);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        -webkit-font-smoothing: antialiased;
        position: relative;
        overflow: hidden;
    }

    /* ── Animated background ─────────────────────────────────── */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 50% at 20% 20%, rgba(124,58,237,0.12) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 80% 80%, rgba(147,51,234,0.08) 0%, transparent 60%);
        pointer-events: none;
        z-index: 0;
    }

    /* Subtle grid */
    body::after {
        content: '';
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(rgba(124,58,237,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(124,58,237,0.04) 1px, transparent 1px);
        background-size: 60px 60px;
        pointer-events: none;
        z-index: 0;
    }

    .auth-wrapper {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 440px;
    }

    /* ── Logo ─────────────────────────────────────────────────── */
    .auth-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-bottom: 32px;
        text-decoration: none;
    }

    .logo-icon {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, var(--accent), #9333ea);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        box-shadow: 0 0 30px rgba(124,58,237,0.4);
    }

    .logo-name {
        font-family: 'Syne', sans-serif;
        font-size: 22px; font-weight: 800;
        letter-spacing: -0.03em;
        background: linear-gradient(135deg, #f0eeff 30%, #a78bfa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* ── Card ─────────────────────────────────────────────────── */
    .auth-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 16px;
        padding: 36px 40px;
        box-shadow:
            0 0 0 1px rgba(124,58,237,0.05),
            0 24px 64px rgba(0,0,0,0.6),
            0 0 80px rgba(124,58,237,0.06);
        backdrop-filter: blur(12px);
    }

    .auth-header {
        margin-bottom: 28px;
    }

    .auth-title {
        font-family: 'Syne', sans-serif;
        font-size: 20px; font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--text-1);
        margin-bottom: 6px;
    }

    .auth-subtitle {
        font-size: 13px;
        color: var(--text-2);
        line-height: 1.5;
    }

    /* ── Form Elements ────────────────────────────────────────── */
    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px; font-weight: 500;
        color: var(--text-2);
        margin-bottom: 7px;
        letter-spacing: 0.02em;
    }

    .form-label a {
        color: var(--accent-light);
        text-decoration: none;
        font-size: 11px;
    }
    .form-label a:hover { text-decoration: underline; }

    .form-input {
        width: 100%;
        padding: 10px 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 9px;
        color: var(--text-1);
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s;
        outline: none;
    }

    .form-input:hover {
        border-color: rgba(124,58,237,0.3);
    }

    .form-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,0.15);
        background: rgba(124,58,237,0.05);
    }

    .form-input::placeholder { color: var(--text-3); }

    .form-input.error {
        border-color: rgba(244,63,94,0.5);
    }
    .form-input.error:focus {
        box-shadow: 0 0 0 3px rgba(244,63,94,0.12);
    }

    .form-error {
        font-size: 11px; color: #fb7185;
        margin-top: 5px;
        display: flex; align-items: center; gap: 4px;
    }

    .form-hint {
        font-size: 11px; color: var(--text-3);
        margin-top: 5px;
        line-height: 1.4;
    }

    /* Checkbox */
    .form-check {
        display: flex; align-items: flex-start; gap: 10px;
        font-size: 13px; color: var(--text-2);
        cursor: pointer;
        line-height: 1.4;
    }

    .form-check input[type="checkbox"] {
        width: 16px; height: 16px;
        accent-color: var(--accent);
        flex-shrink: 0;
        margin-top: 1px;
        cursor: pointer;
    }

    .form-check a { color: var(--accent-light); text-decoration: none; display: contents; }
    .form-check a:hover { text-decoration: underline; }

    /* ── Submit Button ─────────────────────────────────────────── */
    .btn-submit {
        width: 100%;
        padding: 11px 20px;
        background: var(--accent);
        color: white;
        border: none;
        border-radius: 9px;
        font-size: 14px; font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 8px;
        box-shadow: 0 0 24px rgba(124,58,237,0.3);
        letter-spacing: 0.01em;
        position: relative;
        overflow: hidden;
    }

    .btn-submit::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, transparent 0%, rgba(255,255,255,0.07) 50%, transparent 100%);
        transform: translateX(-100%);
        transition: transform 0.5s;
    }

    .btn-submit:hover {
        background: #6d28d9;
        transform: translateY(-1px);
        box-shadow: 0 4px 32px rgba(124,58,237,0.45);
    }

    .btn-submit:hover::after {
        transform: translateX(100%);
    }

    .btn-submit:active { transform: translateY(0); }
    .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    /* ── Divider ───────────────────────────────────────────────── */
    .divider {
        display: flex; align-items: center; gap: 12px;
        margin: 24px 0;
        font-size: 11px; color: var(--text-3);
        text-transform: uppercase; letter-spacing: 0.08em;
    }
    .divider::before, .divider::after {
        content: ''; flex: 1;
        height: 1px;
        background: rgba(255,255,255,0.07);
    }

    /* ── Footer Link ───────────────────────────────────────────── */
    .auth-footer {
        text-align: center;
        margin-top: 24px;
        font-size: 13px;
        color: var(--text-2);
    }
    .auth-footer a {
        color: var(--accent-light);
        text-decoration: none;
        font-weight: 500;
    }
    .auth-footer a:hover { text-decoration: underline; }

    /* ── Alert ─────────────────────────────────────────────────── */
    .alert {
        padding: 12px 16px;
        border-radius: 9px;
        font-size: 13px;
        margin-bottom: 20px;
        line-height: 1.5;
    }
    .alert-danger  { background: rgba(244,63,94,0.08);  border: 1px solid rgba(244,63,94,0.25);  color: #fb7185; }
    .alert-success { background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.25); color: #34d399; }
    .alert-info    { background: rgba(124,58,237,0.08); border: 1px solid rgba(124,58,237,0.25); color: var(--accent-light); }

    /* ── Password strength ─────────────────────────────────────── */
    .strength-bar {
        height: 3px;
        background: rgba(255,255,255,0.08);
        border-radius: 999px;
        margin-top: 6px;
        overflow: hidden;
    }
    .strength-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.3s, background 0.3s;
        width: 0%;
    }

    /* ── Scroll ────────────────────────────────────────────────── */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-thumb { background: rgba(124,58,237,0.3); border-radius: 999px; }
    </style>
</head>
<body>
    <div class="auth-wrapper">

        <a href="{{ url('/') }}" class="auth-logo">
            <img src="{{ asset('images/logo_seomaster.svg') }}" width="240" alt="Logo">

        </a>

        <div class="auth-card">
            {{ $slot }}
        </div>

        @if(isset($footer))
            <div class="auth-footer">{{ $footer }}</div>
        @endif

    </div>
</body>
</html>

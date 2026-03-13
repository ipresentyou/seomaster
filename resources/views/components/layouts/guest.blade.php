<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SEOmaster' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    :root {
        --bg:           #f8f9fa;
        --card-bg:      #fff;
        --card-border:  #dadce0;
        --accent:       #1a73e8;
        --accent-hover: #1765cc;
        --accent-light: #e8f0fe;
        --text-1:       #202124;
        --text-2:       #5f6368;
        --text-3:       #80868b;
        --success:      #1e8e3e;
        --danger:       #d93025;
        --warning:      #e37400;
        --border:       #dadce0;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Roboto', arial, sans-serif;
        background: var(--bg);
        color: var(--text-1);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 24px;
        -webkit-font-smoothing: antialiased;
    }

    .auth-wrapper {
        width: 100%;
        max-width: 400px;
    }

    /* ── Logo ─────────────────────────────────────────────────── */
    .auth-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-bottom: 28px;
        text-decoration: none;
    }

    .logo-icon {
        width: 36px; height: 36px;
        background: var(--accent);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }

    .logo-name {
        font-family: 'Google Sans', sans-serif;
        font-size: 20px; font-weight: 500;
        color: var(--text-1);
        letter-spacing: -0.01em;
    }

    /* ── Card ─────────────────────────────────────────────────── */
    .auth-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 8px;
        padding: 40px 40px 36px;
        box-shadow: 0 1px 3px rgba(60,64,67,.1), 0 4px 8px rgba(60,64,67,.08);
    }

    .auth-header {
        margin-bottom: 24px;
        text-align: center;
    }

    .auth-title {
        font-family: 'Google Sans', sans-serif;
        font-size: 24px; font-weight: 400;
        color: var(--text-1);
        margin-bottom: 8px;
    }

    .auth-subtitle {
        font-size: 14px;
        color: var(--text-2);
        line-height: 1.5;
    }

    /* ── Form Elements ────────────────────────────────────────── */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px; font-weight: 500;
        color: var(--text-1);
        margin-bottom: 6px;
    }

    .form-label a {
        color: var(--accent);
        text-decoration: none;
        font-size: 13px;
        font-weight: 400;
    }
    .form-label a:hover { text-decoration: underline; }

    .form-input {
        width: 100%;
        padding: 10px 14px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 4px;
        color: var(--text-1);
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s;
        outline: none;
    }

    .form-input:hover { border-color: #b8bdc3; }

    .form-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 2px rgba(26,115,232,0.15);
    }

    .form-input::placeholder { color: var(--text-3); }

    .form-input.error { border-color: var(--danger); }
    .form-input.error:focus { box-shadow: 0 0 0 2px rgba(217,48,37,0.12); }

    .form-error {
        font-size: 12px; color: var(--danger);
        margin-top: 5px;
    }

    .form-hint {
        font-size: 12px; color: var(--text-3);
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

    .form-check a { color: var(--accent); text-decoration: none; display: contents; }
    .form-check a:hover { text-decoration: underline; }

    /* ── Submit Button ─────────────────────────────────────────── */
    .btn-submit {
        width: 100%;
        padding: 10px 20px;
        background: var(--accent);
        color: white;
        border: none;
        border-radius: 4px;
        font-family: 'Google Sans', sans-serif;
        font-size: 14px; font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        margin-top: 8px;
        box-shadow: 0 1px 2px rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
    }

    .btn-submit:hover {
        background: var(--accent-hover);
        box-shadow: 0 1px 3px rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
    }

    .btn-submit:active { background: #1558b0; }
    .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

    /* ── Divider ───────────────────────────────────────────────── */
    .divider {
        display: flex; align-items: center; gap: 12px;
        margin: 24px 0;
        font-size: 12px; color: var(--text-3);
        text-transform: uppercase; letter-spacing: 0.06em;
    }
    .divider::before, .divider::after {
        content: ''; flex: 1;
        height: 1px;
        background: var(--border);
    }

    /* ── Footer Link ───────────────────────────────────────────── */
    .auth-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 13px;
        color: var(--text-2);
    }
    .auth-footer a {
        color: var(--accent);
        text-decoration: none;
        font-weight: 500;
    }
    .auth-footer a:hover { text-decoration: underline; }

    /* ── Alert ─────────────────────────────────────────────────── */
    .alert {
        padding: 12px 16px;
        border-radius: 4px;
        font-size: 13px;
        margin-bottom: 20px;
        line-height: 1.5;
    }
    .alert-danger  { background: #fce8e6; border-left: 4px solid var(--danger);  color: #c5221f; }
    .alert-success { background: #e6f4ea; border-left: 4px solid var(--success); color: #137333; }
    .alert-info    { background: var(--accent-light); border-left: 4px solid var(--accent); color: #174ea6; }

    /* ── Password strength ─────────────────────────────────────── */
    .strength-bar {
        height: 3px;
        background: var(--border);
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

    /* ── Footer nav ────────────────────────────────────────────── */
    .page-footer {
        margin-top: 28px;
        display: flex;
        justify-content: center;
        gap: 24px;
        font-size: 12px;
        color: var(--text-3);
    }
    .page-footer a { color: var(--text-3); text-decoration: none; }
    .page-footer a:hover { text-decoration: underline; }
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

        <div class="page-footer">
            <a href="#">Datenschutz</a>
            <a href="#">Impressum</a>
            <a href="#">Hilfe</a>
        </div>

    </div>
</body>
</html>

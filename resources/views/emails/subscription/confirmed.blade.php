<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, sans-serif; background:#f4f4f5; margin:0; padding:24px; }
        .card { background:#fff; border-radius:12px; max-width:520px; margin:0 auto; padding:36px 40px; }
        .logo { font-size:22px; font-weight:700; color:#7c3aed; margin-bottom:28px; }
        h1 { font-size:20px; color:#111; margin:0 0 12px; }
        p { color:#555; font-size:15px; line-height:1.6; margin:0 0 16px; }
        .plan-box { background:#f5f3ff; border:1px solid #e0d9ff; border-radius:8px; padding:16px 20px; margin:20px 0; }
        .plan-name { font-size:17px; font-weight:600; color:#7c3aed; }
        .plan-meta { font-size:13px; color:#6b7280; margin-top:4px; }
        .btn { display:inline-block; background:#7c3aed; color:#fff; padding:12px 28px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; margin-top:8px; }
        .footer { text-align:center; font-size:12px; color:#9ca3af; margin-top:28px; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">⚡ SEOmaster</div>
    <h1>Willkommen! Dein Abonnement ist aktiv. 🎉</h1>
    <p>Hey {{ $subscription->user->name ?? 'da' }},<br>
       dein Abonnement wurde erfolgreich aktiviert. Du hast jetzt vollen Zugriff auf alle SEO-Tools.</p>

    <div class="plan-box">
        <div class="plan-name">{{ $subscription->plan->name ?? 'Pro' }} Plan</div>
        <div class="plan-meta">
            {{ $subscription->billing_cycle === 'yearly' ? 'Jährlich' : 'Monatlich' }} ·
            Aktiv bis {{ $subscription->current_period_end?->format('d.m.Y') ?? '–' }}
        </div>
    </div>

    <p>Verbinde jetzt deinen Shopware-Shop und optimiere deine ersten Produkte.</p>
    <a href="{{ url('/dashboard') }}" class="btn">Zum Dashboard →</a>

    <div class="footer">
        Bei Fragen: support@lavarell.com<br>
        © {{ date('Y') }} SEOmaster — SEO Automation für Shopware
    </div>
</div>
</body>
</html>

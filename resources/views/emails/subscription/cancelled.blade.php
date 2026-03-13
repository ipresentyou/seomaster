<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, sans-serif; background:#f4f4f5; margin:0; padding:24px; }
        .card { background:#fff; border-radius:12px; max-width:520px; margin:0 auto; padding:36px 40px; }
        .logo { text-align:center; margin-bottom:28px; }
        .logo img { width:180px; height:auto; }
        h1 { font-size:20px; color:#111; margin:0 0 12px; }
        p { color:#555; font-size:15px; line-height:1.6; margin:0 0 16px; }
        .info-box { background:#fef3c7; border:1px solid #fde68a; border-radius:8px; padding:16px 20px; margin:20px 0; font-size:14px; color:#92400e; }
        .btn { display:inline-block; background:#7c3aed; color:#fff; padding:12px 28px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; margin-top:8px; }
        .footer { text-align:center; font-size:12px; color:#9ca3af; margin-top:28px; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <img src="{{ asset('images/logo_seomaster.svg') }}" alt="SEOmaster Logo">
    </div>
    <h1>Dein Abonnement wurde gekündigt</h1>
    <p>Hey {{ $subscription->user->name ?? 'da' }},<br>
       deine Kündigung wurde erfolgreich verarbeitet.</p>

    @if ($subscription->current_period_end)
    <div class="info-box">
        ⏳ Dein Zugang bleibt bis zum <strong>{{ $subscription->current_period_end->format('d.m.Y') }}</strong> aktiv.
        Du kannst alle SEO-Tools bis zu diesem Datum weiterhin nutzen.
    </div>
    @endif

    <p>Du kannst dein Abonnement jederzeit wieder aktivieren — deine Projekte und Daten bleiben erhalten.</p>
    <a href="{{ url('/subscription') }}" class="btn">Erneut abonnieren →</a>

    <p style="margin-top:24px; font-size:13px; color:#9ca3af;">
        Warum hast du gekündigt? Schreib uns kurz: <a href="mailto:support@seomaster.de" style="color:#7c3aed;">support@seomaster.de</a>
    </p>

    <div class="footer">
        © {{ date('Y') }} SEOmaster — SEO Automation für Shopware
    </div>
</div>
</body>
</html>

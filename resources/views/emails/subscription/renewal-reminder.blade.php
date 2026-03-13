<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, 'Segoe UI', sans-serif; background: #f4f4f5; margin: 0; padding: 24px; }
        .card { background: #fff; border-radius: 12px; max-width: 520px; margin: 0 auto; padding: 36px 40px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .logo { text-align:center; margin-bottom:28px; }
        .logo img { width:180px; height:auto; }
        h1   { font-size: 20px; color: #111; margin: 0 0 12px; }
        p    { color: #555; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }

        .info-badge {
            display: inline-block;
            background: #eff6ff;
            border: 1px solid #93c5fd;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 999px;
            margin-bottom: 20px;
        }

        .renewal-box {
            background: #f5f3ff;
            border: 1px solid #e0d9ff;
            border-radius: 10px;
            padding: 20px 24px;
            margin: 20px 0;
        }
        .renewal-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            font-size: 14px;
        }
        .renewal-row:not(:last-child) { border-bottom: 1px solid #ede9fe; }
        .renewal-label { color: #6b7280; }
        .renewal-value { font-weight: 600; color: #111; }
        .renewal-price { font-size: 20px; font-weight: 700; color: #7c3aed; }

        .btn-secondary {
            display: inline-block;
            background: transparent;
            color: #7c3aed;
            border: 1.5px solid #7c3aed;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-top: 4px;
        }

        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
        .footer   { text-align: center; font-size: 12px; color: #9ca3af; margin-top: 28px; line-height: 1.8; }
    </style>
</head>
<body>
<div class="card">

    <div class="logo">
        <img src="{{ asset('images/logo_seomaster.svg') }}" alt="SEOmaster Logo">
    </div>

    <div class="info-badge">📅 Verlängerungs-Erinnerung</div>

    <h1>Dein Abonnement verlängert sich in {{ $daysAhead }} Tagen</h1>

    <p>Hey {{ $subscription->user->name ?? 'da' }},</p>
    <p>
        nur eine kurze Info: Dein SEOmaster-Abonnement verlängert sich automatisch
        am <strong>{{ $subscription->current_period_end?->format('d. F Y') ?? '–' }}</strong>.
    </p>

    <div class="renewal-box">
        <div class="renewal-row">
            <span class="renewal-label">Plan</span>
            <span class="renewal-value">{{ $subscription->plan->name ?? '–' }}</span>
        </div>
        <div class="renewal-row">
            <span class="renewal-label">Abrechnungszeitraum</span>
            <span class="renewal-value">
                {{ $subscription->billing_cycle === 'yearly' ? 'Jährlich' : 'Monatlich' }}
            </span>
        </div>
        <div class="renewal-row">
            <span class="renewal-label">Verlängerung am</span>
            <span class="renewal-value">{{ $subscription->current_period_end?->format('d.m.Y') ?? '–' }}</span>
        </div>
        <div class="renewal-row">
            <span class="renewal-label">Betrag</span>
            <span class="renewal-price">
                €{{ $subscription->billing_cycle === 'yearly'
                    ? number_format($subscription->plan->price_yearly, 2, ',', '.')
                    : number_format($subscription->plan->price_monthly, 2, ',', '.') }}
            </span>
        </div>
    </div>

    <p style="font-size:13px; color:#6b7280;">
        Die Zahlung erfolgt automatisch über PayPal. Du musst nichts weiter tun.
        Möchtest du das Abonnement kündigen, kannst du das jederzeit in deinen Einstellungen tun.
    </p>

    <a href="{{ url('/subscription') }}" class="btn-secondary">
        Abonnement verwalten
    </a>

    <hr class="divider">

    <div class="footer">
        Bei Fragen: <a href="mailto:support@seomaster.de" style="color:#7c3aed;">support@seomaster.de</a><br>
        © {{ date('Y') }} SEOmaster — SEO Automation für Shopware
    </div>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, 'Segoe UI', sans-serif; background: #f4f4f5; margin: 0; padding: 24px; }
        .card { background: #fff; border-radius: 12px; max-width: 520px; margin: 0 auto; padding: 36px 40px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .logo { font-size: 22px; font-weight: 700; color: #7c3aed; margin-bottom: 28px; }
        h1 { font-size: 20px; color: #111; margin: 0 0 12px; }
        p { color: #555; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }

        .warning-badge {
            display: inline-block;
            background: #fff8e7;
            border: 1px solid #fcd34d;
            color: #b45309;
            font-size: 13px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 999px;
            margin-bottom: 20px;
        }

        .countdown-box {
            background: #f5f3ff;
            border: 1px solid #e0d9ff;
            border-radius: 10px;
            padding: 20px 24px;
            margin: 20px 0;
            text-align: center;
        }
        .countdown-number {
            font-size: 48px;
            font-weight: 700;
            color: #7c3aed;
            line-height: 1;
        }
        .countdown-label {
            font-size: 14px;
            color: #6b7280;
            margin-top: 6px;
        }

        .plan-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 16px 0;
        }
        .plan-name { font-size: 16px; font-weight: 600; color: #111; }
        .plan-meta { font-size: 13px; color: #6b7280; margin-top: 4px; }

        .features { margin: 16px 0; padding: 0; list-style: none; }
        .features li { font-size: 14px; color: #374151; padding: 4px 0; }
        .features li::before { content: '✓ '; color: #7c3aed; font-weight: 600; }

        .btn {
            display: inline-block;
            background: #7c3aed;
            color: #fff;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            margin-top: 8px;
        }
        .btn:hover { background: #6d28d9; }

        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }

        .footer { text-align: center; font-size: 12px; color: #9ca3af; margin-top: 28px; line-height: 1.8; }
    </style>
</head>
<body>
<div class="card">

    <div class="logo">⚡ SEOmaster</div>

    <div class="warning-badge">
        @if($daysLeft === 1) ⏰ Letzte Chance @else ⏳ Trial läuft ab @endif
    </div>

    <h1>
        @if($daysLeft === 1)
            Dein Trial endet morgen!
        @else
            Noch {{ $daysLeft }} Tage deines Trials
        @endif
    </h1>

    <p>Hey {{ $subscription->user->name ?? 'da' }},</p>
    <p>
        @if($daysLeft === 1)
            morgen endet dein kostenloser SEOmaster-Trial.
            Damit du ohne Unterbrechung weiterarbeiten kannst, aktiviere jetzt dein Abonnement.
        @else
            in {{ $daysLeft }} Tagen endet dein kostenloser SEOmaster-Trial.
            Wähle einen Plan, um weiterhin alle SEO-Tools nutzen zu können.
        @endif
    </p>

    <div class="countdown-box">
        <div class="countdown-number">{{ $daysLeft }}</div>
        <div class="countdown-label">{{ $daysLeft === 1 ? 'Tag verbleibend' : 'Tage verbleibend' }}</div>
    </div>

    <div class="plan-box">
        <div class="plan-name">{{ $subscription->plan->name ?? 'Starter' }} Plan (Trial)</div>
        <div class="plan-meta">
            Trial endet am {{ $subscription->trial_ends_at?->format('d.m.Y') ?? '–' }}
        </div>
    </div>

    <p><strong>Was du nach dem Trial verlierst:</strong></p>
    <ul class="features">
        <li>KI-generierte SEO-Texte für Produkte</li>
        <li>Automatische Meta-Title & Description</li>
        <li>Bild Alt-Text Optimierung</li>
        <li>Kategorien-SEO mit Keywords</li>
    </ul>

    <a href="{{ url('/subscription') }}" class="btn">
        Jetzt Abonnement wählen →
    </a>

    <hr class="divider">

    <div class="footer">
        Fragen? Schreib uns: <a href="mailto:support@lavarell.com" style="color:#7c3aed;">support@lavarell.com</a><br>
        © {{ date('Y') }} SEOmaster — SEO Automation für Shopware
    </div>
</div>
</body>
</html>

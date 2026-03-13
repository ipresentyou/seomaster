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
        h1 { font-size: 20px; color: #111; margin: 0 0 12px; }
        p { color: #555; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }

        .expired-badge {
            display: inline-block;
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            font-size: 13px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 999px;
            margin-bottom: 20px;
        }

        .reactivate-box {
            background: #f5f3ff;
            border: 2px solid #7c3aed;
            border-radius: 10px;
            padding: 24px;
            margin: 20px 0;
            text-align: center;
        }
        .reactivate-title { font-size: 16px; font-weight: 600; color: #111; margin-bottom: 6px; }
        .reactivate-sub   { font-size: 13px; color: #6b7280; }

        .pricing-row {
            display: flex;
            gap: 12px;
            margin: 16px 0;
        }
        .plan-card {
            flex: 1;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px;
            text-align: center;
        }
        .plan-card.recommended { border-color: #7c3aed; background: #faf5ff; }
        .plan-card-name  { font-size: 14px; font-weight: 600; color: #111; }
        .plan-card-price { font-size: 18px; font-weight: 700; color: #7c3aed; margin: 4px 0; }
        .plan-card-cycle { font-size: 11px; color: #9ca3af; }

        .btn {
            display: inline-block;
            background: #7c3aed;
            color: #fff;
            padding: 13px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            margin-top: 8px;
            width: 100%;
            text-align: center;
            box-sizing: border-box;
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

    <div class="expired-badge">🔴 Trial abgelaufen</div>

    <h1>Dein kostenloser Trial ist abgelaufen</h1>

    <p>Hey {{ $subscription->user->name ?? 'da' }},</p>
    <p>
        dein 14-tägiger SEOmaster-Trial ist gestern abgelaufen.
        Dein Account ist noch vorhanden — du kannst jederzeit ein Abonnement starten und
        direkt dort weitermachen, wo du aufgehört hast.
    </p>

    <div class="reactivate-box">
        <div class="reactivate-title">🚀 Jetzt einsteigen</div>
        <div class="reactivate-sub">Alle Deine Projekte und Einstellungen sind noch gespeichert</div>
    </div>

    @php
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->take(3)->get();
    @endphp

    @if($plans->isNotEmpty())
    <div class="pricing-row">
        @foreach($plans as $plan)
        <div class="plan-card {{ $plan->slug === 'pro' ? 'recommended' : '' }}">
            <div class="plan-card-name">{{ $plan->name }}</div>
            <div class="plan-card-price">€{{ number_format($plan->price_monthly, 0) }}</div>
            <div class="plan-card-cycle">/Monat</div>
        </div>
        @endforeach
    </div>
    @endif

    <a href="{{ url('/subscription') }}" class="btn">
        Plan auswählen & starten →
    </a>

    <hr class="divider">

    <p style="font-size:13px; color:#6b7280; text-align:center;">
        Kein Interesse? Dein Account bleibt 30 Tage erhalten und wird dann automatisch gelöscht.<br>
        Daten exportieren? Schreib uns an <a href="mailto:support@seomaster.de" style="color:#7c3aed;">support@seomaster.de</a>
    </p>

    <div class="footer">
        © {{ date('Y') }} SEOmaster — SEO Automation für Shopware
    </div>
</div>
</body>
</html>

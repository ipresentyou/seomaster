<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, 'Segoe UI', sans-serif; background: #f4f4f5; margin: 0; padding: 24px; }
        .card { background: #fff; border-radius: 12px; max-width: 520px; margin: 0 auto; padding: 36px 40px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .logo  { font-size: 22px; font-weight: 700; color: #7c3aed; margin-bottom: 28px; }
        h1     { font-size: 20px; color: #111; margin: 0 0 12px; }
        p      { color: #555; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }

        .urgent-badge {
            display: inline-block;
            font-size: 13px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 999px;
            margin-bottom: 20px;
        }
        .badge-warning { background: #fff8e7; border: 1px solid #fcd34d; color: #b45309; }
        .badge-danger  { background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; }

        .invoice-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 20px 24px;
            margin: 20px 0;
        }
        .invoice-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            font-size: 14px;
        }
        .invoice-row:not(:last-child) { border-bottom: 1px solid #fee2e2; }
        .invoice-label { color: #6b7280; }
        .invoice-value { font-weight: 600; color: #111; }
        .invoice-amount { font-size: 20px; font-weight: 700; color: #dc2626; }

        .grace-bar {
            background: #f3f4f6;
            border-radius: 999px;
            height: 8px;
            margin: 16px 0 6px;
            overflow: hidden;
        }
        .grace-fill {
            height: 100%;
            border-radius: 999px;
            background: #dc2626;
        }
        .grace-label { font-size: 12px; color: #9ca3af; text-align: right; }

        .steps { list-style: none; padding: 0; margin: 16px 0; }
        .steps li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
            color: #374151;
            padding: 6px 0;
        }
        .step-num {
            width: 22px; height: 22px;
            background: #7c3aed;
            color: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700;
            flex-shrink: 0;
        }

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

    <div class="logo">⚡ SEOmaster</div>

    <div class="urgent-badge {{ $daysSinceFailed >= 7 ? 'badge-danger' : 'badge-warning' }}">
        @if($daysSinceFailed >= 7)
            🔴 Letzte Mahnung
        @else
            ⚠️ Zahlung fehlgeschlagen
        @endif
    </div>

    <h1>
        @if($daysSinceFailed >= 7)
            Dein Konto wird in Kürze gesperrt
        @else
            Zahlung konnte nicht verarbeitet werden
        @endif
    </h1>

    <p>Hey {{ $invoice->subscription->user->name ?? 'da' }},</p>
    <p>
        @if($daysSinceFailed >= 7)
            Trotz mehrerer Versuche konnte die Zahlung für dein SEOmaster-Abonnement
            nicht verarbeitet werden. <strong>Bitte aktualisiere deine Zahlungsmethode sofort,
            sonst wird dein Account heute gesperrt.</strong>
        @else
            leider konnte die Zahlung für dein Abonnement nicht verarbeitet werden.
            Bitte aktualisiere deine Zahlungsmethode in PayPal, damit dein Zugang aktiv bleibt.
        @endif
    </p>

    <div class="invoice-box">
        <div class="invoice-row">
            <span class="invoice-label">Rechnungsdatum</span>
            <span class="invoice-value">{{ $invoice->created_at?->format('d.m.Y') ?? '–' }}</span>
        </div>
        <div class="invoice-row">
            <span class="invoice-label">Plan</span>
            <span class="invoice-value">{{ $invoice->subscription->plan->name ?? '–' }}</span>
        </div>
        <div class="invoice-row">
            <span class="invoice-label">Fehlgeschlagener Betrag</span>
            <span class="invoice-amount">€{{ number_format((float) $invoice->amount, 2, ',', '.') }}</span>
        </div>
    </div>

    @php $gracePct = min(100, ($daysSinceFailed / 7) * 100); @endphp
    <div class="grace-bar">
        <div class="grace-fill" style="width: {{ $gracePct }}%;"></div>
    </div>
    <div class="grace-label">{{ $daysSinceFailed }} von 7 Tagen Kulanz verbraucht</div>

    <p style="margin-top:16px;"><strong>So behebst du das Problem:</strong></p>
    <ol class="steps">
        <li>
            <div class="step-num">1</div>
            <span>Logge dich in dein <a href="https://www.paypal.com" style="color:#7c3aed;">PayPal-Konto</a> ein</span>
        </li>
        <li>
            <div class="step-num">2</div>
            <span>Prüfe deine Zahlungsmethode (Karte, Bankverbindung)</span>
        </li>
        <li>
            <div class="step-num">3</div>
            <span>PayPal wird die Zahlung automatisch erneut versuchen</span>
        </li>
    </ol>

    <a href="{{ url('/subscription') }}" class="btn">
        Abonnement & Zahlung verwalten →
    </a>

    <hr class="divider">

    <p style="font-size:13px; color:#6b7280; text-align:center;">
        Probleme? Wir helfen dir gerne:
        <a href="mailto:support@lavarell.com" style="color:#7c3aed;">support@lavarell.com</a>
    </p>

    <div class="footer">
        © {{ date('Y') }} SEOmaster — SEO Automation für Shopware
    </div>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, sans-serif; background:#f4f4f5; margin:0; padding:24px; }
        .card { background:#fff; border-radius:12px; max-width:520px; margin:0 auto; padding:36px 40px; }
        h1 { font-size:20px; color:#111; margin:0 0 12px; }
        p { color:#555; font-size:15px; line-height:1.6; margin:0 0 16px; }
        .invoice-box { background:#f5f3ff; border:1px solid #e0d9ff; border-radius:8px; padding:16px 20px; margin:20px 0; }
        .inv-number { font-size:17px; font-weight:600; color:#7c3aed; }
        .inv-meta { font-size:13px; color:#6b7280; margin-top:4px; }
        .amount { font-size:22px; font-weight:700; color:#111; margin-top:8px; }
        .footer { text-align:center; color:#aaa; font-size:12px; margin-top:24px; }
    </style>
</head>
<body>
<div class="card">
    <h1>📄 Ihre Rechnung ist bereit</h1>
    <p>vielen Dank für Ihr Vertrauen in SEOmaster. Im Anhang finden Sie Ihre Rechnung als PDF und als XRechnung (XML) für Ihre Buchhaltung.</p>

    <div class="invoice-box">
        <div class="inv-number">{{ $invoice->invoice_number }}</div>
        <div class="inv-meta">{{ $invoice->description }}</div>
        <div class="inv-meta">Ausgestellt: {{ $invoice->issued_at->format('d.m.Y') }} · Fällig: {{ $invoice->due_date->format('d.m.Y') }}</div>
        <div class="amount">{{ number_format($invoice->gross_amount, 2, ',', '.') }} {{ $invoice->currency }}</div>
        <div class="inv-meta" style="margin-top:4px;">inkl. {{ number_format($invoice->tax_rate, 0) }} % MwSt. ({{ number_format($invoice->tax_amount, 2, ',', '.') }} {{ $invoice->currency }})</div>
    </div>

    <p>Bitte überweisen Sie den Betrag bis zum <strong>{{ $invoice->due_date->format('d.m.Y') }}</strong> unter Angabe der Rechnungsnummer <strong>{{ $invoice->invoice_number }}</strong>.</p>

    @if($seller['iban'])
    <p><strong>IBAN:</strong> {{ $seller['iban'] }}@if($seller['bic']) &nbsp;·&nbsp; <strong>BIC:</strong> {{ $seller['bic'] }}@endif</p>
    @endif

    <p style="font-size:13px;color:#888;">Die XRechnung-Datei (XML) entspricht dem deutschen Standard EN 16931 / XRechnung 3.0 und kann direkt in Ihre Buchhaltungssoftware importiert werden.</p>

    <div class="footer">
        {{ $seller['company'] }} · {{ $seller['address'] }}, {{ $seller['zip'] }} {{ $seller['city'] }}<br>
        @if($seller['vat_id'])USt-IdNr.: {{ $seller['vat_id'] }}@endif
    </div>
</div>
</body>
</html>

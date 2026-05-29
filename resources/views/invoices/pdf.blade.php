<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1a1a1a; }
.page { padding:40px 50px; }
.header { display:table; width:100%; margin-bottom:40px; }
.logo-col { display:table-cell; width:50%; vertical-align:top; }
.logo-text { font-size:22px; font-weight:bold; color:#7c3aed; }
.logo-sub { font-size:10px; color:#666; margin-top:2px; }
.doc-col { display:table-cell; width:50%; vertical-align:top; text-align:right; }
.doc-title { font-size:20px; font-weight:bold; color:#1a1a1a; }
.doc-number { font-size:13px; color:#7c3aed; margin-top:4px; }

.addresses { display:table; width:100%; margin-bottom:36px; }
.addr-col { display:table-cell; width:50%; vertical-align:top; }
.addr-label { font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#888; margin-bottom:6px; }
.addr-company { font-size:13px; font-weight:bold; margin-bottom:2px; }
.addr-line { font-size:11px; color:#333; line-height:1.6; }

.meta-table { width:100%; border-collapse:collapse; margin-bottom:32px; }
.meta-table td { padding:5px 10px; font-size:11px; }
.meta-table .label { background:#f5f5f5; color:#555; width:160px; }
.meta-table .value { color:#1a1a1a; }

.items-table { width:100%; border-collapse:collapse; margin-bottom:24px; }
.items-table th { background:#7c3aed; color:#fff; padding:8px 10px; text-align:left; font-size:11px; }
.items-table th.right { text-align:right; }
.items-table td { padding:9px 10px; border-bottom:1px solid #eee; font-size:11px; }
.items-table td.right { text-align:right; }
.items-table tr:last-child td { border-bottom:none; }

.totals { margin-left:60%; }
.totals-row { display:table; width:100%; padding:4px 0; }
.totals-label { display:table-cell; text-align:left; color:#555; }
.totals-value { display:table-cell; text-align:right; }
.totals-total { font-size:14px; font-weight:bold; border-top:2px solid #7c3aed; padding-top:6px; margin-top:4px; color:#7c3aed; }

.payment { margin-top:32px; padding:14px 16px; background:#f9f7ff; border-left:3px solid #7c3aed; font-size:11px; }
.payment strong { color:#7c3aed; }

.footer { margin-top:40px; padding-top:16px; border-top:1px solid #eee; font-size:9px; color:#999; text-align:center; line-height:1.8; }
</style>
</head>
<body>
<div class="page">

{{-- Header --}}
<div class="header">
    <div class="logo-col">
        <div class="logo-text">SEOmaster</div>
        <div class="logo-sub">{{ $seller['company'] }}</div>
    </div>
    <div class="doc-col">
        <div class="doc-title">RECHNUNG</div>
        <div class="doc-number">{{ $invoice->invoice_number }}</div>
    </div>
</div>

{{-- Addresses --}}
<div class="addresses">
    <div class="addr-col">
        <div class="addr-label">Rechnungssteller</div>
        <div class="addr-company">{{ $seller['company'] }}</div>
        <div class="addr-line">{{ $seller['address'] }}</div>
        <div class="addr-line">{{ $seller['zip'] }} {{ $seller['city'] }}</div>
        @if($seller['vat_id'])
        <div class="addr-line" style="margin-top:4px;">USt-IdNr.: {{ $seller['vat_id'] }}</div>
        @elseif($seller['tax_number'])
        <div class="addr-line" style="margin-top:4px;">Steuernr.: {{ $seller['tax_number'] }}</div>
        @endif
        @if($seller['email'])
        <div class="addr-line">{{ $seller['email'] }}</div>
        @endif
    </div>
    <div class="addr-col">
        <div class="addr-label">Rechnungsempfänger</div>
        <div class="addr-company">{{ $user->billing_company ?: $user->name }}</div>
        @if($user->billing_name && $user->billing_company)
        <div class="addr-line">{{ $user->billing_name }}</div>
        @endif
        <div class="addr-line">{{ $user->billing_address }}</div>
        <div class="addr-line">{{ $user->billing_zip }} {{ $user->billing_city }}</div>
        <div class="addr-line">{{ $user->billing_country }}</div>
        @if($user->billing_vat_id)
        <div class="addr-line" style="margin-top:4px;">USt-IdNr.: {{ $user->billing_vat_id }}</div>
        @endif
    </div>
</div>

{{-- Meta --}}
<table class="meta-table">
    <tr>
        <td class="label">Rechnungsnummer</td>
        <td class="value">{{ $invoice->invoice_number }}</td>
        <td class="label">Rechnungsdatum</td>
        <td class="value">{{ $invoice->issued_at->format('d.m.Y') }}</td>
    </tr>
    <tr>
        <td class="label">Leistungszeitraum</td>
        <td class="value">{{ $invoice->issued_at->format('M Y') }}</td>
        <td class="label">Fällig am</td>
        <td class="value">{{ $invoice->due_date->format('d.m.Y') }}</td>
    </tr>
</table>

{{-- Line items --}}
<table class="items-table">
    <thead>
        <tr>
            <th>Beschreibung</th>
            <th class="right" style="width:100px;">Netto</th>
            <th class="right" style="width:80px;">MwSt.</th>
            <th class="right" style="width:100px;">Brutto</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $invoice->description }}<br>
                <span style="color:#888;font-size:10px;">Nutzungszeitraum: {{ $invoice->issued_at->format('d.m.Y') }} – {{ $invoice->due_date->subDay()->format('d.m.Y') }}</span>
            </td>
            <td class="right">{{ number_format($invoice->net_amount, 2, ',', '.') }} €</td>
            <td class="right">{{ number_format($invoice->tax_rate, 0) }} %</td>
            <td class="right">{{ number_format($invoice->gross_amount, 2, ',', '.') }} €</td>
        </tr>
    </tbody>
</table>

{{-- Totals --}}
<div class="totals">
    <div class="totals-row">
        <span class="totals-label">Nettobetrag:</span>
        <span class="totals-value">{{ number_format($invoice->net_amount, 2, ',', '.') }} €</span>
    </div>
    <div class="totals-row">
        <span class="totals-label">MwSt. {{ number_format($invoice->tax_rate, 0) }} %:</span>
        <span class="totals-value">{{ number_format($invoice->tax_amount, 2, ',', '.') }} €</span>
    </div>
    <div class="totals-row totals-total">
        <span class="totals-label">Gesamtbetrag:</span>
        <span class="totals-value">{{ number_format($invoice->gross_amount, 2, ',', '.') }} €</span>
    </div>
</div>

{{-- Payment --}}
<div class="payment" style="margin-top:32px;">
    <strong>Zahlungshinweis:</strong> Bitte überweisen Sie den Betrag von
    <strong>{{ number_format($invoice->gross_amount, 2, ',', '.') }} €</strong>
    bis zum <strong>{{ $invoice->due_date->format('d.m.Y') }}</strong> unter Angabe der Rechnungsnummer
    <strong>{{ $invoice->invoice_number }}</strong> auf folgendes Konto:<br>
    IBAN: {{ $seller['iban'] }}@if($seller['bic'])  · BIC: {{ $seller['bic'] }}@endif
</div>

{{-- Footer --}}
<div class="footer">
    {{ $seller['company'] }} · {{ $seller['address'] }}, {{ $seller['zip'] }} {{ $seller['city'] }}
    @if($seller['vat_id']) · USt-IdNr.: {{ $seller['vat_id'] }} @endif
    @if($seller['tax_number']) · Steuernr.: {{ $seller['tax_number'] }} @endif
</div>

</div>
</body>
</html>

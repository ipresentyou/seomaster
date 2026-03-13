<x-layouts.app title="Abonnement">

<style>
/* ── Current Plan Banner ─────────────────────────────── */
.current-plan-banner {
    display: flex; align-items: center; gap: 16px;
    padding: 18px 22px;
    background: linear-gradient(135deg, rgba(124,58,237,0.12), rgba(147,51,234,0.06));
    border: 1px solid rgba(124,58,237,0.3);
    border-radius: 12px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.current-plan-banner::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(167,139,250,0.6), transparent);
}

.plan-badge-big {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, var(--accent), #9333ea);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px;
    box-shadow: 0 0 24px var(--accent-glow);
    flex-shrink: 0;
}

.plan-info { flex: 1; }
.plan-name  { font-size: 18px; font-weight: 700; letter-spacing: -0.02em; }
.plan-meta  { font-size: 12px; color: var(--text-2); margin-top: 3px; display: flex; gap: 12px; }
.plan-meta span { display: flex; align-items: center; gap: 4px; }

.plan-actions { display: flex; gap: 8px; }

/* Trial countdown */
.trial-bar {
    margin-top: 8px;
    background: rgba(255,255,255,0.06);
    border-radius: 99px;
    height: 4px;
    overflow: hidden;
}
.trial-progress {
    height: 100%;
    border-radius: 99px;
    background: linear-gradient(90deg, var(--accent), #9333ea);
    transition: width 0.5s;
}

/* ── Pricing Grid ─────────────────────────────────────── */
.pricing-header {
    text-align: center;
    margin-bottom: 24px;
}

.pricing-header h2 {
    font-size: 24px; font-weight: 700; letter-spacing: -0.03em;
    margin-bottom: 6px;
}

.billing-toggle {
    display: inline-flex;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 99px;
    padding: 3px;
    gap: 2px;
    margin-top: 12px;
}

.toggle-btn {
    padding: 5px 16px;
    border-radius: 99px;
    font-size: 12px; font-weight: 500;
    cursor: pointer; border: none;
    background: none; color: var(--text-2);
    transition: all 0.2s;
}
.toggle-btn.active {
    background: var(--accent);
    color: white;
    box-shadow: 0 0 12px var(--accent-glow);
}

.save-badge {
    background: rgba(16,185,129,0.12);
    color: #34d399; border: 1px solid rgba(16,185,129,0.3);
    font-size: 10px; font-weight: 600;
    padding: 1px 7px; border-radius: 99px;
    margin-left: 6px;
}

/* Pricing cards */
.pricing-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 40px;
}

.pricing-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    padding: 24px;
    display: flex; flex-direction: column;
    position: relative;
    transition: border-color 0.2s, transform 0.2s;
}

.pricing-card:hover { transform: translateY(-3px); }

.pricing-card.popular {
    border-color: rgba(124,58,237,0.5);
    background: linear-gradient(180deg, rgba(124,58,237,0.08), var(--card-bg) 60%);
}

.popular-tag {
    position: absolute; top: -1px; left: 50%; transform: translateX(-50%);
    background: linear-gradient(135deg, var(--accent), #9333ea);
    color: white; font-size: 11px; font-weight: 600;
    padding: 3px 14px;
    border-radius: 0 0 8px 8px;
    letter-spacing: 0.04em;
}

.plan-icon { font-size: 28px; margin-bottom: 10px; }
.plan-title { font-size: 18px; font-weight: 700; letter-spacing: -0.02em; }
.plan-desc  { font-size: 12px; color: var(--text-3); margin-top: 3px; }

.plan-price {
    margin: 18px 0;
    display: flex; align-items: flex-end; gap: 4px;
}

.price-amount {
    font-size: 38px; font-weight: 800;
    letter-spacing: -0.04em; line-height: 1;
}

.price-cur  { font-size: 20px; font-weight: 600; margin-bottom: 4px; }
.price-per  { font-size: 12px; color: var(--text-3); margin-bottom: 5px; }
.price-year { font-size: 11px; color: var(--text-3); display: none; }
.price-year.show-yearly { display: block; }

.feature-list { list-style: none; margin-bottom: 20px; flex: 1; }
.feature-list li {
    display: flex; align-items: flex-start; gap: 8px;
    padding: 5px 0;
    font-size: 13px; color: var(--text-2);
    border-bottom: 1px solid rgba(255,255,255,0.03);
}
.feature-list li:last-child { border-bottom: none; }
.feature-list .check { color: var(--success); flex-shrink: 0; font-size: 14px; }
.feature-list .limit { color: var(--text-3); font-size: 11px; margin-top: 1px; }

.pricing-cta {
    width: 100%; padding: 10px;
    border-radius: 10px;
    font-size: 14px; font-weight: 600;
    cursor: pointer; border: none;
    transition: all 0.2s;
}
.cta-primary {
    background: var(--accent);
    color: white;
    box-shadow: 0 0 20px var(--accent-glow);
}
.cta-primary:hover { background: #6d28d9; transform: translateY(-1px); }

.cta-secondary {
    background: rgba(255,255,255,0.05);
    color: var(--text-2);
    border: 1px solid rgba(255,255,255,0.1);
}
.cta-secondary:hover { background: rgba(255,255,255,0.08); color: var(--text-1); }

.cta-current {
    background: rgba(16,185,129,0.1);
    color: #34d399;
    border: 1px solid rgba(16,185,129,0.3);
    cursor: default;
}

/* ── Invoice History ─────────────────────────────────── */
.invoice-table {
    width: 100%; border-collapse: collapse;
}
.invoice-table th {
    padding: 9px 14px; text-align: left;
    font-size: 10px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-3);
    background: rgba(255,255,255,0.02);
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.invoice-table td {
    padding: 11px 14px;
    font-size: 13px; color: var(--text-2);
    border-bottom: 1px solid rgba(255,255,255,0.03);
}
.invoice-table tr:last-child td { border-bottom: none; }
.invoice-table tr:hover td { color: var(--text-1); }
</style>

<div class="page-header">
    <div>
        <div class="page-title">💳 Abonnement</div>
        <div class="page-subtitle">Verwalte deinen Plan und deine Rechnungen</div>
    </div>
</div>

{{-- ── Current Plan ───────────────────────────────────────── --}}
@if($subscription)
<div class="current-plan-banner">
    <div class="plan-badge-big">
        {{ $subscription->isOnTrial() ? '⏳' : '✨' }}
    </div>
    <div class="plan-info">
        <div class="plan-name">
            {{ $subscription->plan->name ?? 'Plan' }}
            @if($subscription->isOnTrial())
                <span class="badge badge-amber" style="font-size:11px; vertical-align:middle;">Trial</span>
            @else
                <span class="badge badge-green" style="font-size:11px; vertical-align:middle;">Aktiv</span>
            @endif
        </div>
        <div class="plan-meta">
            @if($subscription->isOnTrial())
                <span>⏰ Trial läuft ab: {{ $subscription->trial_ends_at->format('d.m.Y') }}
                    ({{ $subscription->trial_ends_at->diffForHumans() }})</span>
            @elseif($subscription->current_period_end)
                <span>🔄 Verlängert am: {{ $subscription->current_period_end->format('d.m.Y') }}</span>
            @endif
            <span>💳 {{ ucfirst($subscription->billing_cycle ?? 'monatlich') }}</span>
            @if($subscription->plan)
                <span>💶 € {{ number_format($subscription->plan->price_monthly, 2, ',', '.') }}/Monat</span>
            @endif
        </div>

        @if($subscription->isOnTrial())
            @php
                $trialDays = 3;
                $daysLeft = max(0, now()->diffInDays($subscription->trial_ends_at, false));
                $progress = round((1 - $daysLeft / $trialDays) * 100);
            @endphp
            <div class="trial-bar" style="margin-top:10px; max-width:300px;">
                <div class="trial-progress" style="width:{{ $progress }}%"></div>
            </div>
            <div style="font-size:11px;color:var(--text-3);margin-top:4px;">
                {{ $daysLeft }} von {{ $trialDays }} Trial-Tagen verbleiben
            </div>
        @endif
    </div>
    <div class="plan-actions">
        @if($subscription->status !== 'cancelled')
            <form method="POST" action="{{ route('subscription.cancel-plan') }}"
                  onsubmit="return confirm('Abo wirklich kündigen? Es läuft bis Periodenende.')">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm">Kündigen</button>
            </form>
        @else
            <span class="badge badge-red">Gekündigt · läuft ab {{ $subscription->current_period_end?->format('d.m.Y') }}</span>
        @endif
    </div>
</div>
@endif

{{-- ── Pricing Toggle ─────────────────────────────────────── --}}
<div class="pricing-header">
    <h2>Plan wählen oder wechseln</h2>
    <div style="color:var(--text-3);font-size:13px;">Starter Plan: 3 Tage gratis testen | Pro & Agency: PayPal Abrechnung</div>
    <div style="display:flex;align-items:center;justify-content:center;margin-top:10px;">
        <div class="billing-toggle">
            <button class="toggle-btn active" id="btn-monthly" onclick="setBilling('monthly')">Monatlich</button>
            <button class="toggle-btn" id="btn-yearly" onclick="setBilling('yearly')">
                Jährlich <span class="save-badge">-17%</span>
            </button>
        </div>
    </div>
</div>

{{-- ── Pricing Cards ──────────────────────────────────────── --}}
<div class="pricing-grid">
    @php
        $popularSlug = 'pro';
        $icons = ['starter' => '🚀', 'pro' => '⚡', 'agency' => '🏢'];
        $descriptions = [
            'starter' => 'Perfekt für einzelne Shops',
            'pro'     => 'Für wachsende Händler',
            'agency'  => 'Für SEO-Agenturen',
        ];
    @endphp

    @foreach($plans as $plan)
        @php
            $isPopular  = $plan->slug === $popularSlug;
            $isCurrent  = $subscription && $subscription->subscription_plan_id === $plan->id
                          && $subscription->isActive();
        @endphp

        <div class="pricing-card {{ $isPopular ? 'popular' : '' }}">
            @if($isPopular)
                <div class="popular-tag">BELIEBT</div>
            @endif

            <div class="plan-icon">{{ $icons[$plan->slug] ?? '📦' }}</div>
            <div class="plan-title">{{ $plan->name }}</div>
            <div class="plan-desc">{{ $descriptions[$plan->slug] ?? $plan->description }}</div>

            <div class="plan-price">
                <span class="price-cur">€</span>
                <span class="price-amount monthly-price">{{ number_format($plan->price_monthly, 0) }}</span>
                <div>
                    <div class="price-per">/Monat</div>
                    <div class="price-year {{ 'price-year-' . $plan->id }}">
                        € {{ number_format($plan->price_yearly ?? $plan->price_monthly * 10, 0) }}/Jahr
                    </div>
                </div>
            </div>

            <ul class="feature-list">
                <li>
                    <span class="check">✓</span>
                    <span>{{ $plan->max_shops }} {{ $plan->max_shops === 1 ? 'Shop' : 'Shops' }} verbinden</span>
                </li>
                <li>
                    <span class="check">✓</span>
                    <span>{{ number_format($plan->max_api_calls_per_day) }} API-Calls/Tag</span>
                </li>
                @foreach(($plan->features ?? []) as $feature)
                    @php
                        $featureLabels = [
                            'seo_products'   => ['label' => 'Produkte SEO', 'icon' => '🏷️'],
                            'seo_categories' => ['label' => 'Kategorien SEO', 'icon' => '📁'],
                            'alt_text'       => ['label' => 'Alt-Text Generator (KI)', 'icon' => '🖼️'],
                            'gsc_dashboard'  => ['label' => 'GSC Dashboard', 'icon' => '📊'],
                            'bulk_generate'  => ['label' => 'Bulk-Generierung', 'icon' => '⚡'],
                            'export_csv'     => ['label' => 'CSV Export', 'icon' => '📤'],
                        ];
                        $fl = $featureLabels[$feature] ?? ['label' => $feature, 'icon' => '✓'];
                    @endphp
                    <li>
                        <span class="check">{{ $fl['icon'] }}</span>
                        <span>{{ $fl['label'] }}</span>
                    </li>
                @endforeach
            </ul>

            @if($isCurrent)
                <button class="pricing-cta cta-current">✓ Aktueller Plan</button>
            @elseif($plan->slug === 'starter')
                {{-- Starter Plan: Local Trial --}}
                <a href="{{ route('subscription.start-trial') }}" 
                   class="pricing-cta {{ $isPopular ? 'cta-primary' : 'cta-secondary' }}">
                    3 Tage gratis testen
                </a>
            @else
                {{-- Pro/Agency Plans: PayPal Checkout --}}
                <form method="POST" action="{{ route('subscription.checkout') }}">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <input type="hidden" name="billing_cycle" id="cycle-{{ $plan->id }}" value="monthly">
                    <button type="submit"
                            class="pricing-cta {{ $isPopular ? 'cta-primary' : 'cta-secondary' }}">
                        @if($subscription && $subscription->isActive())
                            Wechseln zu {{ $plan->name }}
                        @else
                            Mit PayPal starten
                        @endif
                    </button>
                </form>
            @endif
        </div>
    @endforeach
</div>

{{-- ── Invoice History ────────────────────────────────────── --}}
@if($invoices->isNotEmpty())
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">🧾 Rechnungshistorie</div>
            <div class="card-subtitle">{{ $invoices->count() }} Zahlungen</div>
        </div>
    </div>
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Datum</th>
                <th>Plan</th>
                <th>Betrag</th>
                <th>Status</th>
                <th>PayPal TX</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $inv)
                <tr>
                    <td>{{ $inv->paid_at?->format('d.m.Y') ?? '—' }}</td>
                    <td>{{ $inv->subscription->plan->name ?? '—' }}</td>
                    <td style="font-weight:600; color:var(--text-1)">
                        € {{ number_format($inv->amount, 2, ',', '.') }}
                    </td>
                    <td>
                        @if($inv->status === 'paid')
                            <span class="badge badge-green">✓ Bezahlt</span>
                        @elseif($inv->status === 'refunded')
                            <span class="badge badge-amber">↩ Erstattet</span>
                        @else
                            <span class="badge badge-red">✗ Fehlgeschlagen</span>
                        @endif
                    </td>
                    <td style="font-family:monospace; font-size:11px; color:var(--text-3)">
                        {{ $inv->paypal_transaction_id ?? '—' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="card">
    <div style="padding:32px; text-align:center; color:var(--text-3);">
        <div style="font-size:32px; margin-bottom:8px;">🧾</div>
        Noch keine Zahlungen vorhanden
    </div>
</div>
@endif

{{-- FAQ --}}
<div style="margin-top:32px; display:grid; grid-template-columns:1fr 1fr; gap:12px;">
    @foreach([
        ['❓', 'Kann ich jederzeit kündigen?', 'Ja. Du kannst dein Abo jederzeit kündigen. Es bleibt bis zum Ende des aktuellen Abrechnungszeitraums aktiv.'],
        ['🔄', 'Was passiert nach dem Trial?', 'Nach dem 3-tägigen Trial für den Starter Plan musst du einen Pro oder Agency Plan wählen, um weiter Zugriff zu haben.'],
        ['💳', 'Welche Zahlungsmethoden?', 'Aktuell PayPal und PayPal-Kreditkarte für Pro & Agency Pläne. Starter Plan hat lokales Trial.'],
        ['📤', 'Kann ich meinen Plan wechseln?', 'Ja. Du kannst jederzeit upgraden oder downgraden. Die Änderung wird sofort wirksam.'],
    ] as $faq)
    <div style="background:var(--card-bg); border:1px solid var(--card-border); border-radius:10px; padding:16px;">
        <div style="display:flex; gap:10px; align-items:flex-start;">
            <span style="font-size:16px">{{ $faq[0] }}</span>
            <div>
                <div style="font-size:13px; font-weight:600; margin-bottom:4px;">{{ $faq[1] }}</div>
                <div style="font-size:12px; color:var(--text-3); line-height:1.5;">{{ $faq[2] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
let currentCycle = 'monthly';

function setBilling(cycle) {
    currentCycle = cycle;

    document.getElementById('btn-monthly').classList.toggle('active', cycle === 'monthly');
    document.getElementById('btn-yearly').classList.toggle('active',  cycle === 'yearly');

    // Preise umschalten
    document.querySelectorAll('.monthly-price').forEach((el, i) => {
        const prices = @json($plans->pluck('price_monthly', 'id'));
        const yearlyPrices = @json($plans->pluck('price_yearly', 'id'));
    });

    // Cycle-Hidden-Inputs updaten
    document.querySelectorAll('[id^="cycle-"]').forEach(input => {
        input.value = cycle;
    });

    // Jährlicher Hinweis zeigen
    document.querySelectorAll('[class*="price-year-"]').forEach(el => {
        el.classList.toggle('show-yearly', cycle === 'yearly');
    });
}
</script>
@endpush

</x-layouts.app>

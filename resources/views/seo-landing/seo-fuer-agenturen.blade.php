@extends('layouts.public')

@section('title', 'SEO für Agenturen – mehrere Shopware-Shops zentral optimieren')
@section('meta_description', 'SEOmaster für Agenturen: Bis zu 20 Shopware-Shops zentral verwalten und per KI optimieren – Meta-Tags, Produktbeschreibungen, Alt-Texte und Audits für alle Kundenprojekte an einem Ort.')

@section('content')
@include('seo-landing._styles')

<div class="sl-hero">
    <div class="sl-eyebrow">✦ Für SEO- &amp; Digitalagenturen</div>
    <h1>SEO für Agenturen – alle Kundenshops an einem Ort optimieren</h1>
    <p class="sl-sub">
        Statt für jeden Kundenshop einzeln SEO-Texte zu schreiben, verwalten Sie mit SEOmaster
        alle Shopware-Projekte Ihrer Agentur zentral – Meta-Tags, Produktbeschreibungen und
        Alt-Texte werden für jeden angebundenen Shop automatisch per KI generiert.
    </p>
    <div class="sl-cta-row">
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Kostenlos starten →</a>
        <a href="{{ route('subscription.index') }}" class="sl-btn sl-btn-outline">Agency-Plan ansehen</a>
    </div>
</div>

<div class="sl-body">
    <h2>Der Skalierungs-Engpass bei mehreren Kundenprojekten</h2>
    <p>
        Wer als Agentur SEO für mehrere Shopware-Shops gleichzeitig betreut, kennt das Problem:
        Jeder Kundenshop hat hunderte Produkte, jedes davon braucht individuelle Meta-Tags,
        Beschreibungen und Alt-Texte. Von Hand ist das über mehrere Projekte hinweg nicht mehr
        wirtschaftlich zu leisten.
    </p>

    <div class="sl-box">
        <h3>Der Agency-Plan im Überblick</h3>
        <ul style="margin:0; padding-left:20px;">
            <li>Bis zu 20 Shopware-Shops gleichzeitig verbinden</li>
            <li>2.000 API-Calls pro Tag</li>
            <li>White-Label-Option für den Auftritt gegenüber Ihren Kunden</li>
            <li>CSV-Import &amp; Bulk-Export für alle Projekte</li>
            <li>Priorisierter Support</li>
        </ul>
    </div>

    <h2>So arbeiten Agenturen mit SEOmaster</h2>
    <ul>
        <li>Jeden Kundenshop als eigenes Projekt anlegen und per API verbinden</li>
        <li>Pro Projekt automatisch <a href="{{ route('landing.seo-audit') }}" style="color:#1a73e8;">fehlende SEO-Daten analysieren</a></li>
        <li>Meta-Tags, Produktbeschreibungen und Alt-Texte per KI generieren lassen – projektweise oder in Bulk</li>
        <li>Ergebnisse review­en und mit einem Klick in den jeweiligen Shop zurückschreiben</li>
    </ul>

    <h2>Zeitersparnis, die sich rechnet</h2>
    <p>
        Statt SEO-Texte manuell zu schreiben oder an Freelancer auszulagern, übernimmt SEOmaster
        die Erstarbeit automatisiert – Ihr Team konzentriert sich auf Strategie, Review und die
        Kundenbeziehung statt auf repetitive Textarbeit über mehrere Shops hinweg.
    </p>

    <div class="sl-cta-box">
        <h2>Bereit, Ihre Kundenprojekte zu skalieren?</h2>
        <p>14 Tage kostenlos testen, keine Kreditkarte nötig.</p>
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Jetzt kostenlos starten →</a>
    </div>
</div>

@include('seo-landing._related', ['currentSlug' => 'seo-fuer-agenturen'])
@endsection

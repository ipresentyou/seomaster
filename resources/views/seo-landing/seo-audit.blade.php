@extends('layouts.public')

@section('title', 'SEO Audit für Shopware – fehlende SEO-Daten automatisch finden')
@section('meta_description', 'SEO Audit für Ihren Shopware-Shop: SEOmaster analysiert automatisch alle Produkte, Kategorien und Bilder auf fehlende Meta-Tags, Beschreibungen und Alt-Texte – und behebt sie direkt per KI.')

@section('content')
@include('seo-landing._styles')

<div class="sl-hero">
    <div class="sl-eyebrow">✦ Automatische Analyse</div>
    <h1>SEO Audit für Shopware – Lücken finden, bevor Google sie findet</h1>
    <p class="sl-sub">
        Bevor SEOmaster optimiert, analysiert es Ihren Shop: Welche Produkte haben keinen
        Meta-Titel? Welche Kategorien keine Beschreibung? Welche Bilder keinen Alt-Text?
        Der Audit läuft automatisch über die Shopware-API – ganz ohne manuelle Prüfung.
    </p>
    <div class="sl-cta-row">
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Kostenlos starten →</a>
        <a href="{{ url('/#features') }}" class="sl-btn sl-btn-outline">Features ansehen</a>
    </div>
</div>

<div class="sl-body">
    <h2>Warum ein SEO-Audit der erste Schritt sein sollte</h2>
    <p>
        Ohne Überblick lässt sich SEO nicht sinnvoll priorisieren. Bei Shops mit hunderten
        Produkten ist unklar, wo die größten Lücken liegen – fehlen bei 10 % oder bei 80 % der
        Produkte die Meta-Beschreibungen? SEOmaster beantwortet das automatisch.
    </p>

    <div class="sl-box">
        <h3>Was der Audit prüft</h3>
        <p style="margin:0;">
            Meta-Titel und -Beschreibungen bei Produkten und Kategorien, Vorhandensein von
            SEO-Texten sowie fehlende Alt-Texte bei Produktbildern – jeweils mit direkter
            Kennzeichnung, was fehlt und was schon optimiert ist.
        </p>
    </div>

    <h2>So läuft die Analyse ab</h2>
    <ul>
        <li>Shop verbinden – SEOmaster liest alle relevanten Daten über die Shopware-API aus</li>
        <li>Jedes Produkt und jede Kategorie wird auf fehlende SEO-Daten geprüft</li>
        <li>Ergebnis: klare Übersicht mit ✓ erledigt und ⚠ fehlt</li>
        <li>Direkt aus der Analyse heraus per KI optimieren, ohne Tool-Wechsel</li>
    </ul>

    <h2>Vom Audit zur fertigen Optimierung</h2>
    <p>
        Anders als klassische SEO-Audit-Tools bleibt es bei SEOmaster nicht bei der Analyse:
        erkannte Lücken lassen sich direkt mit
        <a href="{{ route('landing.meta-tags-optimieren') }}" style="color:#1a73e8;">automatisch generierten Meta-Tags</a>,
        <a href="{{ route('landing.produktbeschreibungen-seo') }}" style="color:#1a73e8;">Produktbeschreibungen</a>
        und <a href="{{ route('landing.alt-text-generator') }}" style="color:#1a73e8;">Alt-Texten</a> schließen.
    </p>

    <div class="sl-cta-box">
        <h2>Finden Sie heraus, wo Ihr Shop SEO-technisch steht</h2>
        <p>14 Tage kostenlos testen, keine Kreditkarte nötig.</p>
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Jetzt kostenlos starten →</a>
    </div>
</div>

@include('seo-landing._related', ['currentSlug' => 'seo-audit'])
@endsection

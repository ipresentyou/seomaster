@extends('layouts.public')

@section('title', 'Shopware SEO – Automatische Optimierung mit KI')
@section('meta_description', 'Shopware SEO leicht gemacht: SEOmaster analysiert Ihren Shopware-6-Shop und optimiert Meta-Titel, Beschreibungen, Keywords und Alt-Texte automatisch per KI. Jetzt kostenlos testen.')

@section('content')
@include('seo-landing._styles')

<div class="sl-hero">
    <div class="sl-eyebrow">✦ Exklusiv für Shopware 6</div>
    <h1>Shopware SEO – endlich ohne Handarbeit</h1>
    <p class="sl-sub">
        Ein Shopware-Shop mit hunderten Produkten und Kategorien braucht genauso viele SEO-Texte.
        SEOmaster verbindet sich direkt mit Ihrer Shopware-API und übernimmt die komplette
        SEO-Optimierung automatisch – Meta-Titel, Beschreibungen, Keywords und Alt-Texte inklusive.
    </p>
    <div class="sl-cta-row">
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Kostenlos starten →</a>
        <a href="{{ url('/#features') }}" class="sl-btn sl-btn-outline">Features ansehen</a>
    </div>
</div>

<div class="sl-body">
    <h2>Warum Shopware SEO so aufwendig ist</h2>
    <p>
        Shopware liefert von Haus aus keine automatische SEO-Optimierung mit. Jedes Produkt, jede
        Kategorie und jedes Bild braucht einen eigenen Meta-Titel, eine eigene Beschreibung und
        einen sprechenden Alt-Text – von Hand ist das bei größeren Sortimenten schlicht nicht zu
        schaffen.
    </p>

    <div class="sl-box">
        <h3>So arbeitet SEOmaster mit Shopware zusammen</h3>
        <p style="margin:0;">
            Über die offizielle Shopware-6-Admin-API liest SEOmaster Ihre Produkte, Kategorien und
            Bilder aus, erkennt fehlende SEO-Daten automatisch und generiert passende Texte per KI –
            direkt zurückgeschrieben in Ihren Shop, ohne Plugin-Installation.
        </p>
    </div>

    <h2>Was SEOmaster für Ihren Shop übernimmt</h2>
    <ul>
        <li><strong>Meta-Titel &amp; Meta-Beschreibungen</strong> für Produkte und Kategorien, in korrekter Länge (50–60 / 150–160 Zeichen)</li>
        <li><strong>SEO-optimierte Produktbeschreibungen</strong>, die Kunden überzeugen und bei Google ranken</li>
        <li><strong>Alt-Texte für Produktbilder</strong>, automatisch generiert per KI</li>
        <li><strong>Analyse fehlender SEO-Daten</strong> über den kompletten Shop hinweg</li>
    </ul>

    <h2>In wenigen Minuten startklar</h2>
    <p>
        Shopware-Zugangsdaten hinterlegen, Shop verbinden, loslegen. Keine Entwicklerkenntnisse
        nötig, keine Änderungen am Shopsystem selbst – SEOmaster arbeitet ausschließlich über die
        offizielle API.
    </p>

    <div class="sl-cta-box">
        <h2>Bereit für besseres Shopware SEO?</h2>
        <p>14 Tage kostenlos testen, keine Kreditkarte nötig.</p>
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Jetzt kostenlos starten →</a>
    </div>
</div>

@include('seo-landing._related', ['currentSlug' => 'shopware-seo'])
@endsection

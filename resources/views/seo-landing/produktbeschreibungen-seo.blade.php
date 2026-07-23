@extends('layouts.public')

@section('title', 'Produktbeschreibungen SEO – KI-Texte für Shopware')
@section('meta_description', 'SEO-optimierte Produktbeschreibungen für Shopware, automatisch per KI generiert. SEOmaster schreibt überzeugende, keyword-optimierte Texte für Ihr gesamtes Sortiment – in Minuten statt Wochen.')

@section('content')
@include('seo-landing._styles')

<div class="sl-hero">
    <div class="sl-eyebrow">✦ KI-gestützt</div>
    <h1>Produktbeschreibungen SEO – die von Google und Kunden geliebt werden</h1>
    <p class="sl-sub">
        Gute Produktbeschreibungen verkaufen und ranken. SEOmaster generiert für jedes Produkt in
        Ihrem Shopware-Shop einen individuellen, SEO-optimierten Beschreibungstext – basierend auf
        den echten Produktdaten aus Ihrem Shop.
    </p>
    <div class="sl-cta-row">
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Kostenlos starten →</a>
        <a href="{{ url('/#features') }}" class="sl-btn sl-btn-outline">Features ansehen</a>
    </div>
</div>

<div class="sl-body">
    <h2>Duplicate Content ist ein SEO-Risiko</h2>
    <p>
        Viele Shops übernehmen Herstellertexte 1:1 – das Ergebnis sind identische Beschreibungen
        wie bei Dutzenden Wettbewerbern. Google bewertet solchen Duplicate Content schlechter,
        Kunden vertrauen generischen Texten weniger.
    </p>

    <div class="sl-box">
        <h3>Individuelle Texte statt Copy-Paste</h3>
        <p style="margin:0;">
            SEOmaster erstellt für jedes Produkt einen eigenständigen Text, der auf den konkreten
            Produktdaten (Name, Kategorie, Eigenschaften) basiert – einzigartig, keyword-optimiert
            und in Ihrem gewünschten Tonfall.
        </p>
    </div>

    <h2>So läuft die Texterstellung ab</h2>
    <ul>
        <li>SEOmaster liest Produktdaten direkt aus Shopware aus</li>
        <li>Die KI generiert einen individuellen SEO-Text je Produkt</li>
        <li>Sie prüfen, bearbeiten bei Bedarf, und übernehmen den Text mit einem Klick</li>
        <li>Auch für große Sortimente in Bulk möglich</li>
    </ul>

    <h2>Ergänzt durch Meta-Tags und Alt-Texte</h2>
    <p>
        Produktbeschreibungen sind nur ein Baustein. SEOmaster optimiert im selben Workflow auch
        <a href="{{ route('landing.meta-tags-optimieren') }}" style="color:#1a73e8;">Meta-Titel und -Beschreibungen</a>
        sowie <a href="{{ route('landing.alt-text-generator') }}" style="color:#1a73e8;">Bild-Alt-Texte</a>
        – für ein vollständig optimiertes Produkt.
    </p>

    <div class="sl-cta-box">
        <h2>Bessere Produkttexte, weniger Aufwand</h2>
        <p>14 Tage kostenlos testen, keine Kreditkarte nötig.</p>
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Jetzt kostenlos starten →</a>
    </div>
</div>

@include('seo-landing._related', ['currentSlug' => 'produktbeschreibungen-seo'])
@endsection

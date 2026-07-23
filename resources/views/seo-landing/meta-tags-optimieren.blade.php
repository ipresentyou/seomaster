@extends('layouts.public')

@section('title', 'Meta-Tags optimieren – Meta-Titel & Beschreibungen per KI')
@section('meta_description', 'Meta-Tags optimieren ohne Handarbeit: SEOmaster generiert Meta-Titel und Meta-Beschreibungen für alle Produkte und Kategorien in Ihrem Shopware-Shop automatisch per KI, in korrekter Länge.')

@section('content')
@include('seo-landing._styles')

<div class="sl-hero">
    <div class="sl-eyebrow">✦ KI-gestützt</div>
    <h1>Meta-Tags optimieren – automatisch statt mühsam von Hand</h1>
    <p class="sl-sub">
        Meta-Titel und Meta-Beschreibungen entscheiden mit darüber, ob Nutzer in den
        Google-Suchergebnissen auf Ihren Shop klicken. SEOmaster generiert beides für jedes
        Produkt und jede Kategorie automatisch – in der richtigen Länge und mit den relevanten
        Keywords.
    </p>
    <div class="sl-cta-row">
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Kostenlos starten →</a>
        <a href="{{ url('/#features') }}" class="sl-btn sl-btn-outline">Features ansehen</a>
    </div>
</div>

<div class="sl-body">
    <h2>Warum Meta-Tags über Klicks entscheiden</h2>
    <p>
        Der Meta-Titel erscheint als blaue Überschrift im Suchergebnis, die Meta-Beschreibung
        als Vorschautext darunter. Fehlen beide oder sind sie schlecht formuliert, generiert Google
        oft einen wenig überzeugenden Text automatisch – das kostet Klicks und damit Umsatz.
    </p>

    <div class="sl-box">
        <h3>Die richtige Länge zählt</h3>
        <p style="margin:0;">
            Meta-Titel: 50–60 Zeichen. Meta-Beschreibung: 150–160 Zeichen. SEOmaster hält sich
            automatisch an diese Grenzen, damit Ihre Texte in der Google-Suche nicht abgeschnitten
            werden.
        </p>
    </div>

    <h2>So funktioniert die Optimierung mit SEOmaster</h2>
    <ul>
        <li>Shop verbinden – SEOmaster liest alle Produkte und Kategorien über die Shopware-API aus</li>
        <li>Fehlende oder schwache Meta-Tags werden automatisch erkannt</li>
        <li>Die KI generiert individuelle Meta-Titel und -Beschreibungen je Produkt/Kategorie</li>
        <li>Ein Klick genügt, um die Texte direkt in Shopware zu übernehmen</li>
    </ul>

    <h2>Mehr als nur Meta-Tags</h2>
    <p>
        Neben Meta-Titeln und -Beschreibungen optimiert SEOmaster auch
        <a href="{{ route('landing.produktbeschreibungen-seo') }}" style="color:#1a73e8;">Produktbeschreibungen</a>
        und generiert <a href="{{ route('landing.alt-text-generator') }}" style="color:#1a73e8;">Alt-Texte für Bilder</a> –
        alles aus einem Tool, ohne zusätzliches Plugin.
    </p>

    <div class="sl-cta-box">
        <h2>Meta-Tags in Minuten statt Wochen</h2>
        <p>3 Tage kostenlos testen, keine Kreditkarte nötig.</p>
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Jetzt kostenlos starten →</a>
    </div>
</div>

@include('seo-landing._related', ['currentSlug' => 'meta-tags-optimieren'])
@endsection

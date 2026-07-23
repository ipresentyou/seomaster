@extends('layouts.public')

@section('title', 'Alt-Text Generator für Shopware – automatisch per KI')
@section('meta_description', 'Alt-Text Generator für Shopware: SEOmaster erstellt automatisch aussagekräftige Alt-Texte für alle Produktbilder per KI – besser für Bilder-SEO und Barrierefreiheit.')

@section('content')
@include('seo-landing._styles')

<div class="sl-hero">
    <div class="sl-eyebrow">✦ KI-gestützt</div>
    <h1>Alt-Text Generator – Bilder-SEO ohne manuelle Arbeit</h1>
    <p class="sl-sub">
        Fehlende Alt-Texte sind einer der häufigsten SEO-Fehler in Shopware-Shops. SEOmaster
        durchsucht Ihre Mediathek, findet Bilder ohne Alt-Text und generiert automatisch
        passende, beschreibende Texte per KI.
    </p>
    <div class="sl-cta-row">
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Kostenlos starten →</a>
        <a href="{{ url('/#features') }}" class="sl-btn sl-btn-outline">Features ansehen</a>
    </div>
</div>

<div class="sl-body">
    <h2>Warum Alt-Texte wichtig sind</h2>
    <p>
        Alt-Texte helfen Google, den Inhalt eines Bildes zu verstehen – wichtig für die
        Bildersuche und das allgemeine Ranking. Gleichzeitig sind sie essenziell für
        Barrierefreiheit, da Screenreader sie Nutzer:innen mit Sehbeeinträchtigung vorlesen.
        In vielen Shops fehlen sie trotzdem bei einem Großteil der Bilder.
    </p>

    <div class="sl-box">
        <h3>So findet SEOmaster fehlende Alt-Texte</h3>
        <p style="margin:0;">
            Über die Shopware-Mediathek-API erkennt SEOmaster automatisch, welche Bilder noch
            keinen oder einen unzureichenden Alt-Text haben – gefiltert nach Produkt, SVGs werden
            automatisch ausgeblendet.
        </p>
    </div>

    <h2>So funktioniert die Generierung</h2>
    <ul>
        <li>SEOmaster zeigt alle Bilder mit fehlendem Alt-Text in einer Übersicht</li>
        <li>Die KI erkennt den Bildinhalt und generiert einen passenden, beschreibenden Alt-Text</li>
        <li>Einzeln prüfen und speichern, oder als Batch für viele Bilder gleichzeitig</li>
        <li>Änderungen werden direkt in Shopware übernommen</li>
    </ul>

    <h2>Teil der kompletten SEO-Optimierung</h2>
    <p>
        Alt-Texte sind einer von mehreren Bausteinen. SEOmaster optimiert im selben Tool auch
        <a href="{{ route('landing.meta-tags-optimieren') }}" style="color:#1a73e8;">Meta-Tags</a>
        und <a href="{{ route('landing.produktbeschreibungen-seo') }}" style="color:#1a73e8;">Produktbeschreibungen</a> –
        für ein rundum optimiertes Produkt.
    </p>

    <div class="sl-cta-box">
        <h2>Alt-Texte für Ihren ganzen Shop, automatisch</h2>
        <p>3 Tage kostenlos testen, keine Kreditkarte nötig.</p>
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Jetzt kostenlos starten →</a>
    </div>
</div>

@include('seo-landing._related', ['currentSlug' => 'alt-text-generator'])
@endsection

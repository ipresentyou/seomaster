@extends('layouts.public')

@section('title', 'Kategorie-SEO für Shopware – strukturierte Kategorietexte per KI')
@section('meta_description', 'Kategorie-SEO für Shopware: SEOmaster generiert strukturierte, SEO-optimierte Kategoriebeschreibungen per KI – überzeugend für Kunden und gut rankend bei Google.')

@section('content')
@include('seo-landing._styles')

<div class="sl-hero">
    <div class="sl-eyebrow">✦ KI-gestützt</div>
    <h1>Kategorie-SEO – die am meisten unterschätzte SEO-Baustelle</h1>
    <p class="sl-sub">
        Kategorieseiten sind oft die stärksten Rankingseiten eines Shops – werden aber meist ohne
        eigenen Text ausgeliefert. SEOmaster generiert strukturierte, SEO-optimierte
        Kategoriebeschreibungen, die Kunden überzeugen und Google Kontext liefern.
    </p>
    <div class="sl-cta-row">
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Kostenlos starten →</a>
        <a href="{{ url('/#features') }}" class="sl-btn sl-btn-outline">Features ansehen</a>
    </div>
</div>

<div class="sl-body">
    <h2>Warum Kategorieseiten SEO-technisch so wertvoll sind</h2>
    <p>
        Eine Kategorieseite wie „Laufschuhe Damen" bündelt Suchvolumen, das keine einzelne
        Produktseite erreicht. Ohne eigenen Beschreibungstext bleibt dieses Potenzial jedoch oft
        ungenutzt – die Seite besteht nur aus einer Produktliste ohne Kontext für Google.
    </p>

    <div class="sl-box">
        <h3>So generiert SEOmaster Kategorietexte</h3>
        <p style="margin:0;">
            Über die Shopware-API liest SEOmaster Kategoriename, Struktur und enthaltene Produkte
            aus und erstellt daraus einen strukturierten, individuellen Beschreibungstext –
            inklusive Meta-Titel und Meta-Beschreibung für die Kategorieseite selbst.
        </p>
    </div>

    <h2>So läuft die Optimierung ab</h2>
    <ul>
        <li>Kategorien werden automatisch aus Shopware eingelesen, inklusive Unterkategorien</li>
        <li>SEOmaster erkennt, welche Kategorien noch keinen oder einen schwachen Text haben</li>
        <li>Die KI generiert einen individuellen Text je Kategorie, passend zur Marke</li>
        <li>Direktes Zurückschreiben nach Shopware, ohne Copy-Paste</li>
    </ul>

    <h2>Konsistent über den ganzen Shop</h2>
    <p>
        Kategorietexte funktionieren am besten im Zusammenspiel mit optimierten
        <a href="{{ route('landing.produktbeschreibungen-seo') }}" style="color:#1a73e8;">Produktbeschreibungen</a>
        und <a href="{{ route('landing.meta-tags-optimieren') }}" style="color:#1a73e8;">Meta-Tags</a> –
        SEOmaster deckt alle drei Ebenen im selben Tool ab.
    </p>

    <div class="sl-cta-box">
        <h2>Holen Sie das SEO-Potenzial Ihrer Kategorieseiten raus</h2>
        <p>3 Tage kostenlos testen, keine Kreditkarte nötig.</p>
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Jetzt kostenlos starten →</a>
    </div>
</div>

@include('seo-landing._related', ['currentSlug' => 'kategorie-seo'])
@endsection

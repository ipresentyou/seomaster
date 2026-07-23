@extends('layouts.public')

@section('title', 'Shopware SEO ohne Plugin – direkt per API')
@section('meta_description', 'SEOmaster optimiert Ihren Shopware-Shop ohne Plugin-Installation – direkte Anbindung über die offizielle Shopware-6-Admin-API. Änderungen landen sofort im Shop.')

@section('content')
@include('seo-landing._styles')

<div class="sl-hero">
    <div class="sl-eyebrow">✦ Direkte Shopware-API</div>
    <h1>Shopware SEO ohne Plugin-Installation</h1>
    <p class="sl-sub">
        Kein Plugin, kein Update-Risiko, kein Shop-Downtime für die Installation. SEOmaster
        verbindet sich direkt über die offizielle Shopware-6-Admin-API mit Ihrem Shop – SEO-Texte
        landen sofort im System, ganz ohne zusätzliche Erweiterung.
    </p>
    <div class="sl-cta-row">
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Kostenlos starten →</a>
        <a href="{{ url('/#features') }}" class="sl-btn sl-btn-outline">Features ansehen</a>
    </div>
</div>

<div class="sl-body">
    <h2>Warum viele Shopware-SEO-Tools ein Plugin verlangen</h2>
    <p>
        Klassische SEO-Erweiterungen für Shopware müssen im Shop installiert, konfiguriert und bei
        jedem Shopware-Update mitgepflegt werden. Das bedeutet zusätzlichen Wartungsaufwand und ein
        Risiko bei größeren Versionssprüngen.
    </p>

    <div class="sl-box">
        <h3>So funktioniert die API-Anbindung</h3>
        <p style="margin:0;">
            Sie hinterlegen einmalig Ihre Shopware-API-Zugangsdaten (Client-ID und Client-Secret aus
            den Integrationen in Shopware) – SEOmaster liest darüber Produkte, Kategorien und
            Bilder aus und schreibt generierte SEO-Texte direkt zurück. Am Shopsystem selbst ändert
            sich nichts.
        </p>
    </div>

    <h2>Vorteile der plugin-freien Anbindung</h2>
    <ul>
        <li>Keine Installation im Shopware-Backend nötig</li>
        <li>Kein Konflikt mit anderen Plugins oder individuellen Erweiterungen</li>
        <li>Kompatibel mit Shopware-Updates, ohne dass SEOmaster selbst aktualisiert werden muss</li>
        <li>Zugriff lässt sich jederzeit über die Shopware-Integrationseinstellungen widerrufen</li>
    </ul>

    <h2>In wenigen Minuten verbunden</h2>
    <p>
        API-Zugangsdaten in Shopware unter „Einstellungen → System → Integrationen" anlegen, in
        SEOmaster hinterlegen, fertig. Von dort aus stehen sofort
        <a href="{{ route('landing.meta-tags-optimieren') }}" style="color:#1a73e8;">Meta-Tags</a>,
        <a href="{{ route('landing.kategorie-seo') }}" style="color:#1a73e8;">Kategorietexte</a>
        und <a href="{{ route('landing.alt-text-generator') }}" style="color:#1a73e8;">Alt-Texte</a>
        zur Optimierung bereit.
    </p>

    <div class="sl-cta-box">
        <h2>Shop verbinden, ohne etwas zu installieren</h2>
        <p>3 Tage kostenlos testen, keine Kreditkarte nötig.</p>
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Jetzt kostenlos starten →</a>
    </div>
</div>

@include('seo-landing._related', ['currentSlug' => 'shopware-seo-ohne-plugin'])
@endsection

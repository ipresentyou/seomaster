@extends('layouts.public')

@section('title', 'Mehrsprachiges SEO für Shopware – automatisch pro Sprache optimiert')
@section('meta_description', 'Mehrsprachiges SEO für Shopware: SEOmaster erkennt automatisch alle Sprachversionen Ihres Shops und generiert Meta-Tags, Produkttexte und Alt-Texte in jeder Sprache separat.')

@section('content')
@include('seo-landing._styles')

<div class="sl-hero">
    <div class="sl-eyebrow">✦ Mehrsprachiger Support</div>
    <h1>Mehrsprachiges SEO – pro Sprache automatisch optimiert</h1>
    <p class="sl-sub">
        Ein internationaler Shopware-Shop braucht SEO-Texte in jeder Sprachversion – nicht nur
        übersetzt, sondern eigenständig formuliert. SEOmaster erkennt alle in Shopware
        konfigurierten Sprachen automatisch und optimiert jede davon separat.
    </p>
    <div class="sl-cta-row">
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Kostenlos starten →</a>
        <a href="{{ url('/#features') }}" class="sl-btn sl-btn-outline">Features ansehen</a>
    </div>
</div>

<div class="sl-body">
    <h2>Warum reine Übersetzung nicht reicht</h2>
    <p>
        SEO-Texte, die 1:1 übersetzt statt sprachspezifisch formuliert werden, wirken oft unnatürlich
        und treffen selten die tatsächlichen Suchbegriffe der jeweiligen Zielsprache. Für gutes SEO
        braucht jede Sprachversion eigene, für diesen Markt passende Texte.
    </p>

    <div class="sl-box">
        <h3>Automatische Spracherkennung über die Shopware-API</h3>
        <p style="margin:0;">
            SEOmaster liest alle in Shopware angelegten Sprachen und Sales Channels aus. Sie wählen
            die gewünschte Sprache im Tool aus, die KI generiert den Text direkt in dieser Sprache –
            kein manueller Sprachwechsel, keine externe Übersetzung nötig.
        </p>
    </div>

    <h2>So funktioniert die mehrsprachige Optimierung</h2>
    <ul>
        <li>Sprachen und Sales Channels werden automatisch aus Shopware übernommen</li>
        <li>Meta-Tags, Produktbeschreibungen und Alt-Texte lassen sich pro Sprache unabhängig generieren</li>
        <li>Der Brand-Prompt für konsistenten Ton gilt sprachübergreifend im selben Projekt</li>
        <li>Änderungen werden direkt in die jeweilige Sprachversion in Shopware zurückgeschrieben</li>
    </ul>

    <h2>Ein Tool für alle Märkte</h2>
    <p>
        Statt für jede Sprache ein separates Tool oder einen externen Übersetzungsdienst zu nutzen,
        läuft die komplette SEO-Optimierung – von
        <a href="{{ route('landing.meta-tags-optimieren') }}" style="color:#1a73e8;">Meta-Tags</a>
        bis <a href="{{ route('landing.alt-text-generator') }}" style="color:#1a73e8;">Alt-Texten</a> –
        für alle Sprachversionen zentral in SEOmaster.
    </p>

    <div class="sl-cta-box">
        <h2>SEO für jede Sprachversion Ihres Shops</h2>
        <p>3 Tage kostenlos testen, keine Kreditkarte nötig.</p>
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Jetzt kostenlos starten →</a>
    </div>
</div>

@include('seo-landing._related', ['currentSlug' => 'mehrsprachiges-seo'])
@endsection

@extends('layouts.public')

@section('title', 'KI-SEO-Texte mit Brand-Voice – konsistent statt generisch')
@section('meta_description', 'SEOmaster generiert SEO-Texte, die nach Ihrer Marke klingen statt nach generischer KI. Einmal Brand-Prompt hinterlegen, alle Texte im Projekt bleiben konsistent im Ton.')

@section('content')
@include('seo-landing._styles')

<div class="sl-hero">
    <div class="sl-eyebrow">✦ KI mit Brand-Voice</div>
    <h1>SEO-Texte, die nach Ihrer Marke klingen – nicht nach ChatGPT</h1>
    <p class="sl-sub">
        Generische KI-Texte erkennt man sofort – und Kunden auch. Mit SEOmaster hinterlegen Sie
        einmalig einen Brand-Prompt pro Projekt, und alle generierten Meta-Tags, Produkttexte und
        Alt-Texte bleiben konsistent im Ton Ihrer Marke.
    </p>
    <div class="sl-cta-row">
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Kostenlos starten →</a>
        <a href="{{ url('/#features') }}" class="sl-btn sl-btn-outline">Features ansehen</a>
    </div>
</div>

<div class="sl-body">
    <h2>Das Problem mit generischer KI</h2>
    <p>
        Standard-KI-Textgeneratoren schreiben in einem neutralen, austauschbaren Stil – unabhängig
        davon, ob es sich um einen Premium-Anbieter oder einen Budget-Shop handelt. Über Dutzende
        oder hunderte Produkte hinweg summiert sich das zu Texten, die keine erkennbare Markenstimme
        haben.
    </p>

    <div class="sl-box">
        <h3>Ein Prompt, konsistente Texte im ganzen Projekt</h3>
        <p style="margin:0;">
            In den Projekteinstellungen hinterlegen Sie einmalig einen SEO-Prompt – Tonfall,
            Zielgruppe, Besonderheiten Ihrer Marke. Jeder generierte Text im Projekt, egal ob
            Meta-Beschreibung oder Produkttext, berücksichtigt diese Vorgaben automatisch.
        </p>
    </div>

    <h2>Was Sie im Brand-Prompt festlegen können</h2>
    <ul>
        <li>Tonalität: förmlich, locker, verkaufsorientiert oder informativ</li>
        <li>Zielgruppe und Markenpositionierung</li>
        <li>Wiederkehrende Formulierungen oder Begriffe, die verwendet (oder vermieden) werden sollen</li>
        <li>Gilt projektweit – für Produkte, Kategorien und Alt-Texte gleichermaßen</li>
    </ul>

    <h2>Volle Kontrolle bleibt bei Ihnen</h2>
    <p>
        Der Brand-Prompt ist ein Startpunkt, kein Zwang: Jeder generierte Text lässt sich vor dem
        Speichern noch anpassen. So kombinieren Sie die Geschwindigkeit der KI mit der
        Qualitätskontrolle, die Ihre Marke verdient – egal ob bei
        <a href="{{ route('landing.produktbeschreibungen-seo') }}" style="color:#1a73e8;">Produktbeschreibungen</a>
        oder <a href="{{ route('landing.kategorie-seo') }}" style="color:#1a73e8;">Kategorietexten</a>.
    </p>

    <div class="sl-cta-box">
        <h2>SEO-Texte, die wirklich nach Ihnen klingen</h2>
        <p>3 Tage kostenlos testen, keine Kreditkarte nötig.</p>
        <a href="{{ route('register') }}" class="sl-btn sl-btn-primary">Jetzt kostenlos starten →</a>
    </div>
</div>

@include('seo-landing._related', ['currentSlug' => 'ki-brand-voice'])
@endsection

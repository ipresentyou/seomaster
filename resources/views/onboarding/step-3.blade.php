<x-layouts.onboarding :step="$step" :steps="$steps" :progress="$progress" :totalSteps="$totalSteps" title="Erstes Projekt">

@php
    $credentials = \App\Models\ApiCredential::where('user_id', auth()->id())
        ->where('provider', 'shopware')
        ->where('is_active', true)
        ->get();

    $hasCredentials = $credentials->isNotEmpty();
@endphp

<div class="ob-step-badge">
    <span>📁</span> Schritt 3 von {{ $totalSteps }}
</div>

<h1 class="ob-title">Erstes Projekt<br>anlegen</h1>

<p class="ob-subtitle">
    Projekte fassen einen Shop mit deinen SEO-Einstellungen zusammen.
    Du kannst jederzeit weitere Projekte hinzufügen.
</p>

@if(! $hasCredentials)
    {{-- No credentials — show hint but still allow project creation (they can connect later) --}}
    <div class="alert alert-info">
        ℹ Du hast noch keinen Shop verbunden. Du kannst das Projekt trotzdem anlegen
        und die Shop-Verbindung später in den Einstellungen hinzufügen.
    </div>
@endif

<form method="POST" action="{{ route('onboarding.project.save') }}">
    @csrf

    <div class="form-group">
        <label class="form-label" for="proj_name">PROJEKTNAME</label>
        <input
            type="text"
            id="proj_name"
            name="name"
            class="form-input {{ $errors->has('name') ? 'error' : '' }}"
            value="{{ old('name', $hasCredentials ? $credentials->first()->label : '') }}"
            placeholder="z.B. Hauptshop DE"
            autofocus
        >
        @error('name')
            <div class="form-error">⚠ {{ $message }}</div>
        @enderror
    </div>

    @if($hasCredentials)
        <div class="form-group">
            <label class="form-label" for="credential_id">SHOP-VERBINDUNG</label>
            <select id="credential_id" name="credential_id" class="form-select">
                @foreach($credentials as $cred)
                    <option value="{{ $cred->id }}" {{ old('credential_id') == $cred->id ? 'selected' : '' }}>
                        {{ $cred->label }} — {{ $cred->shop_url }}
                    </option>
                @endforeach
            </select>
            @error('credential_id')
                <div class="form-error">⚠ {{ $message }}</div>
            @enderror
        </div>
    @else
        {{-- Hidden placeholder so the controller doesn't break --}}
        <input type="hidden" name="credential_id" value="">
    @endif

    <div class="form-group">
        <label class="form-label" for="description">
            BESCHREIBUNG
            <span style="color:var(--text-3); font-weight:400;">(optional)</span>
        </label>
        <input
            type="text"
            id="description"
            name="description"
            class="form-input"
            value="{{ old('description') }}"
            placeholder="z.B. Hauptshop für den deutschen Markt"
        >
    </div>

    {{-- Features preview --}}
    <div style="
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 24px;
        margin-top: 4px;
    ">
        @foreach([
            ['🏷️', 'Produkt-SEO', 'Titel, Description, Texte'],
            ['📁', 'Kategorie-SEO', 'Keywords, Meta, Texte'],
            ['🖼️', 'Bild Alt-Texte', 'KI-generierte Alt-Texte'],
            ['🔍', 'Analyse', 'Live-Seitencheck'],
        ] as [$icon, $label, $sub])
            <div style="
                background: rgba(255,255,255,0.03);
                border: 1px solid rgba(255,255,255,0.07);
                border-radius: 10px;
                padding: 12px 14px;
            ">
                <div style="font-size:18px; margin-bottom:4px;">{{ $icon }}</div>
                <div style="font-size:13px; font-weight:500; color:var(--text-1);">{{ $label }}</div>
                <div style="font-size:11px; color:var(--text-3); margin-top:2px;">{{ $sub }}</div>
            </div>
        @endforeach
    </div>

    <button type="submit" class="btn-next">
        🚀 Projekt erstellen & fertig
    </button>

    <button type="submit" name="skip" value="1" class="btn-skip-step">
        Überspringen — direkt zum Dashboard
    </button>
</form>

</x-layouts.onboarding>

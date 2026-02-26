<x-layouts.app title="Neues Projekt">

<div style="max-width:600px;">

    <div style="margin-bottom:28px;">
        <a href="{{ route('projects.index') }}" style="font-size:13px; color:var(--text-3); text-decoration:none;">← Zurück zu Projekte</a>
        <h2 style="font-size:22px; font-weight:600; color:var(--text-1); margin:10px 0 4px;">🌐 Neues Projekt</h2>
        <p style="font-size:14px; color:var(--text-2);">Verbinde einen Shopware-Shop mit SEOmaster.</p>
    </div>

    <form action="{{ route('projects.store') }}" method="POST">
        @csrf

        <div class="card" style="padding:24px; display:flex; flex-direction:column; gap:20px;">

            {{-- Project Name --}}
            <div>
                <label class="form-label">Projektname *</label>
                <input type="text" name="name" class="form-input @error('name') border-red @enderror"
                       value="{{ old('name') }}" placeholder="z.B. Hauptshop DE" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Shopware URL --}}
            <div>
                <label class="form-label">Shopware Shop-URL *</label>
                <input type="url" name="shopware_url" class="form-input @error('shopware_url') border-red @enderror"
                       value="{{ old('shopware_url') }}" placeholder="https://mein-shop.de" required>
                <div style="font-size:12px; color:var(--text-3); margin-top:4px;">Die Basis-URL deines Shops (ohne /api).</div>
                @error('shopware_url')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Credential Link --}}
            @if ($credentials->isNotEmpty())
            <div>
                <label class="form-label">Shopware API-Zugangsdaten</label>
                <select name="shopware_credential_id" class="form-select">
                    <option value="">— Automatisch ermitteln —</option>
                    @foreach ($credentials as $cred)
                        <option value="{{ $cred->id }}" {{ old('shopware_credential_id') == $cred->id ? 'selected' : '' }}>
                            {{ $cred->label ?: $cred->getKey('shop_url', 'Shopware') }}
                        </option>
                    @endforeach
                </select>
                <div style="font-size:12px; color:var(--text-3); margin-top:4px;">Falls leer, wird die erste aktive Shopware-Verbindung genutzt.</div>
            </div>
            @else
            <div class="alert alert-warning">
                ⚠️ Keine Shopware-Verbindung gefunden.
                <a href="{{ route('credentials.create') }}" style="color:var(--warning);">Jetzt verbinden →</a>
            </div>
            @endif

        </div>

        <div style="display:flex; gap:12px; margin-top:20px;">
            <button type="submit" class="btn btn-primary">🌐 Projekt erstellen</button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary" style="text-decoration:none;">Abbrechen</a>
        </div>

    </form>

</div>

</x-layouts.app>

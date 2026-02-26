<x-layouts.app title="Projekt bearbeiten – {{ $project->name }}">

<div style="max-width:600px;">

    <div style="margin-bottom:28px;">
        <a href="{{ route('projects.index') }}" style="font-size:13px; color:var(--text-3); text-decoration:none;">← Zurück zu Projekte</a>
        <h2 style="font-size:22px; font-weight:600; color:var(--text-1); margin:10px 0 4px;">✏️ {{ $project->name }}</h2>
        <p style="font-size:14px; color:var(--text-2);">Projekteinstellungen bearbeiten.</p>
    </div>

    <form action="{{ route('projects.update', $project) }}" method="POST">
        @csrf @method('PUT')

        <div class="card" style="padding:24px; display:flex; flex-direction:column; gap:20px;">

            <div>
                <label class="form-label">Projektname *</label>
                <input type="text" name="name" class="form-input"
                       value="{{ old('name', $project->name) }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Shopware Shop-URL *</label>
                <input type="url" name="shopware_url" class="form-input"
                       value="{{ old('shopware_url', $project->shopware_url) }}" required>
                @error('shopware_url')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            @if ($credentials->isNotEmpty())
            <div>
                <label class="form-label">Shopware API-Zugangsdaten</label>
                <select name="shopware_credential_id" class="form-select">
                    <option value="">— Automatisch ermitteln —</option>
                    @foreach ($credentials as $cred)
                        <option value="{{ $cred->id }}"
                            {{ old('shopware_credential_id', $project->shopware_credential_id) == $cred->id ? 'selected' : '' }}>
                            {{ $cred->label ?: $cred->getKey('shop_url', 'Shopware') }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div style="display:flex; align-items:center; gap:10px;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       style="width:16px; height:16px; accent-color:var(--accent);"
                       {{ old('is_active', $project->is_active) ? 'checked' : '' }}>
                <label for="is_active" style="font-size:14px; color:var(--text-1); cursor:pointer;">Projekt aktiv</label>
            </div>

        </div>

        <div style="display:flex; gap:12px; margin-top:20px;">
            <button type="submit" class="btn btn-primary">💾 Änderungen speichern</button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary" style="text-decoration:none;">Abbrechen</a>
        </div>

    </form>

</div>

</x-layouts.app>

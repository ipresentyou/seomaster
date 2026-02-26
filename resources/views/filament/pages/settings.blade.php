<x-filament-panels::page>

    {{-- Page Header Info --}}
    <div class="mb-6 rounded-xl border border-violet-500/20 bg-violet-500/5 p-4">
        <div class="flex items-start gap-3">
            <x-heroicon-o-information-circle class="mt-0.5 h-5 w-5 flex-shrink-0 text-violet-400" />
            <div>
                <p class="text-sm font-medium text-violet-300">Plattform-Einstellungen</p>
                <p class="mt-0.5 text-xs text-slate-400">
                    Änderungen werden sofort wirksam. Sensible Einstellungen wie PayPal-Keys
                    werden über die <code class="text-violet-400">.env</code> verwaltet.
                </p>
            </div>
        </div>
    </div>

    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex items-center justify-between border-t border-white/5 pt-6">
            <p class="text-xs text-slate-500">
                Zuletzt gespeichert: {{ now()->format('d.m.Y H:i') }} Uhr
            </p>
            <x-filament::button type="submit" icon="heroicon-o-check">
                Einstellungen speichern
            </x-filament::button>
        </div>
    </form>

    <x-filament-actions::modals />

</x-filament-panels::page>

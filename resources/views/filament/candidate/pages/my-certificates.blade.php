<x-filament-panels::page>
    <x-filament-panels::form wire:submit="updateCertificates">
        {{ $this->editCertificatesForm }}
        <x-filament-panels::form.actions
            :actions="$this->getUpdateCertificatesFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page>

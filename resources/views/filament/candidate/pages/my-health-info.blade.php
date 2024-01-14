<x-filament-panels::page>
    <x-filament-panels::form wire:submit="updateHealthProfile">
        {{ $this->editHealthProfileForm }}
        <x-filament-panels::form.actions
            :actions="$this->getUpdateHealthProfileFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page>

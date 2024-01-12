<x-filament-panels::page>
    <style>
        .fi-ta-content > .items-center
        {
            display: none;
        }
    </style>
    <div>
        @livewire(\App\Filament\Widgets\MyVehiclesTableWidget::class)
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <style>
        .fi-checkbox-input {
            color: green !important;
        }

        /* Center-align the contents of the parent div */
        .centered-content {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center !important;
        }

        /* Center-align child divs horizontally */
        .centered-child {
            margin: auto !important;
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
            width: 100% !important; /* Take up full width by default */
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            img {
                display: inline-block;
                width: 50% !important;
            }
        }

        @media (min-width: 769px) {
            .centered-child {
                flex: 50% !important;
                min-width: 50% !important;
            }

            img {
                width: 20% !important;
            }
        }
    </style>

    <div class="centered-content">
        <div class="centered-child">
            <x-filament-panels::form wire:submit="updateDriverProfile">
                {{ $this->editDriverProfileForm }}
                <x-filament-panels::form.actions :actions="$this->getUpdateDriverProfileFormActions()" />
            </x-filament-panels::form>
        </div>
        <div class="centered-child">
            <img src="{{ $this->compositeImage }}" alt="Composite Image">
        </div>
    </div>
</x-filament-panels::page>

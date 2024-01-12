<?php

namespace App\Filament\Candidate\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class MyVehicles extends Page
{
    protected static ?string $navigationIcon = 'fas-motorcycle';

    protected static string $view = 'filament.candidate.pages.my-vehicles';

    protected static ?string $navigationGroup = 'My Profile';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('general.my_vehicles');
    }

    public function getTitle(): string|Htmlable
    {
        return __('general.my_vehicles');
    }

    public static function getLabel(): ?string
    {
        return __('general.my_vehicles');
    }
}

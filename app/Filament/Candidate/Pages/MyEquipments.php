<?php

namespace App\Filament\Candidate\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class MyEquipments extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.candidate.pages.my-equipments';

    protected static ?string $navigationGroup = 'My Profile';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('general.my_equipments');
    }

    public function getTitle(): string|Htmlable
    {
        return __('general.my_equipments');
    }

    public static function getLabel(): ?string
    {
        return __('general.my_equipments');
    }
}

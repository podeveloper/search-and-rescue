<?php

namespace App\Filament\Candidate\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class MyHealthInfo extends Page
{
    protected static ?string $navigationIcon = 'fas-heart-pulse';

    protected static string $view = 'filament.candidate.pages.my-health-info';

    protected static ?string $navigationGroup = 'My Profile';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('general.my_health_info');
    }

    public function getTitle(): string|Htmlable
    {
        return __('general.my_health_info');
    }

    public static function getLabel(): ?string
    {
        return __('general.my_health_info');
    }
}

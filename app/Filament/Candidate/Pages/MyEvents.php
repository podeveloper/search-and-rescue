<?php

namespace App\Filament\Candidate\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class MyEvents extends Page
{
    protected static ?string $navigationIcon = 'fas-calendar-check';

    protected static string $view = 'filament.candidate.pages.my-events';

    protected static ?string $navigationGroup = 'My Profile';

    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return __('general.my_events');
    }

    public function getTitle(): string|Htmlable
    {
        return __('general.my_events');
    }

    public static function getLabel(): ?string
    {
        return __('general.my_events');
    }
}

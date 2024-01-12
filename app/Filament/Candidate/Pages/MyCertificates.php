<?php

namespace App\Filament\Candidate\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class MyCertificates extends Page
{
    protected static ?string $navigationIcon = 'fas-award';

    protected static string $view = 'filament.candidate.pages.my-certificates';

    protected static ?string $navigationGroup = 'My Profile';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('general.my_certificates');
    }

    public function getTitle(): string|Htmlable
    {
        return __('general.my_certificates');
    }

    public static function getLabel(): ?string
    {
        return __('general.my_certificates');
    }
}

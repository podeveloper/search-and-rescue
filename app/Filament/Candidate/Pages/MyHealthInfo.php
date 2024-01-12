<?php

namespace App\Filament\Candidate\Pages;

use Filament\Pages\Page;

class MyHealthInfo extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.candidate.pages.my-health-info';

    protected static ?string $navigationGroup = 'My Profile';
    protected static ?int $navigationSort = 6;
}

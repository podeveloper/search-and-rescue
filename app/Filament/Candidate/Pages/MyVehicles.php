<?php

namespace App\Filament\Candidate\Pages;

use Filament\Pages\Page;

class MyVehicles extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.candidate.pages.my-vehicles';

    protected static ?string $navigationGroup = 'My Profile';
    protected static ?int $navigationSort = 4;
}

<?php

namespace App\Filament\Candidate\Pages;

use Filament\Pages\Page;

class MyEvents extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.candidate.pages.my-events';

    protected static ?string $navigationGroup = 'My Profile';
    protected static ?int $navigationSort = 7;
}

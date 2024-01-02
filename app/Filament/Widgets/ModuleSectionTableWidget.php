<?php

namespace App\Filament\Widgets;

use App\Models\Section;
use App\Models\UserProgress;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ModuleSectionTableWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Section::whereHas('module.training', function ($query) {
                    $query->where('id', '=', request()->get('id'));
                })
            )
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('title')
                        ->icon(fn (Section $section) => $section->viewable() ? 'heroicon-m-lock-open': 'heroicon-m-lock-closed')
                        ->url(fn (Section $section) => route('sections.show',$section))
                ]),
            ])
            ->paginated(false)
            ->defaultGroup('module.title')
            ->groups([
                Tables\Grouping\Group::make('module.title')
                    ->collapsible(),
            ])
            ->actions([
                //
            ]);
    }
}


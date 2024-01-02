<?php

namespace App\Filament\Widgets;

use App\Models\Training;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TrainingsTableWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Training::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->weight(FontWeight::Bold),
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('trainingCategory.name'),
                ]),
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('registered_at')
                        ->label(__('general.enrolled'))
                        ->badge()
                        ->icon('heroicon-m-arrow-uturn-right')
                        ->getStateUsing(fn (Training $training) => __('general.enrolled').': '.$training->registered_at_by(auth()->user()->id)),
                ])->extraAttributes(['style' => 'margin: 10px 0px']),
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('finished_at')
                        ->label(__('general.finished'))
                        ->badge()
                        ->color('success')
                        ->icon('heroicon-m-check')
                        ->getStateUsing(fn (Training $training) => __('general.finished').': '.$training->finished_at_by(auth()->user()->id)),
                ])->extraAttributes(['style' => 'margin: 10px 0px']),

            ])
            ->actions([
                Tables\Actions\Action::make('enroll')
                    ->label(__('general.enroll'))
                    ->url(fn (Training $training) => route('trainings.enroll',$training))
                    ->button()
                    ->visible(fn (Training $training) => !$training->users->contains(auth()->user()->id)),
                Tables\Actions\Action::make('view')
                    ->extraAttributes(['class'=>'rounded-lg px-2.5 py-1.5 shadow-sm bg-custom-600 text-white'])
                    ->url(fn (Training $training) => route('filament.volunteer.pages.training-detail',["id=".$training->id]))
                    ->visible(fn (Training $training) => $training->users->contains(auth()->user()->id) && $training),
            ]);
    }
}

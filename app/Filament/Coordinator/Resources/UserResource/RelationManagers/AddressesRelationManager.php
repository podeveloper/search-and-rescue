<?php

namespace App\Filament\Coordinator\Resources\UserResource\RelationManagers;

use App\Models\City;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options([
                        'home' => 'Home',
                        'work' => 'Work',
                        'other' => 'Other',
                    ]),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->live(),
                Forms\Components\Select::make('city_id')
                    ->options(fn(Forms\Get $get): Collection => City::query()
                        ->where('country_id',$get('country_id'))
                        ->pluck('name','id'))
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('district_id')
                    ->options(fn(Forms\Get $get): Collection => District::query()
                        ->where('city_id',$get('city_id'))
                        ->pluck('name','id'))
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('full_address')
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('user_id')->hidden()
                    ->extraInputAttributes(['value' => request()->user()->id]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('type')->searchable(),
                Tables\Columns\TextColumn::make('country.name')->searchable(),
                Tables\Columns\TextColumn::make('city.name')->searchable(),
                Tables\Columns\TextColumn::make('district.name')->searchable(),
                Tables\Columns\TextColumn::make('full_address')->searchable(),
                Tables\Columns\TextColumn::make('user.full_name')->searchable(),
                Tables\Columns\TextColumn::make('distance_from_center')
                    ->label(__('general.distance_km'))
                    ->formatStateUsing(fn($state)=> $state > 0 ? number_format($state / 1000,2) : $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_time_of_arrival')
                    ->label(__('general.estimated_time_min'))
                    ->formatStateUsing(function($state){
                        if ($state > 0)
                        {
                            return number_format($state / 60,2);
                        }

                        return null;
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}

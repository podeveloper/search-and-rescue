<?php

namespace App\Filament\Reference\Resources;

use App\Filament\Coordinator\Resources\RegistrationQuestionResource\Pages;
use App\Filament\Coordinator\Resources\RegistrationQuestionResource\RelationManagers;
use App\Models\RegistrationQuestion;
use App\Traits\NavigationLocalizationTrait;
use Archilex\ToggleIconColumn\Columns\ToggleIconColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class RegistrationQuestionResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = RegistrationQuestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Registartion Questions';

    protected static ?int $navigationSort = 98;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('text')
                    ->required()
                    ->columnSpan('full')
                    ->required()
                    ->string()
                    ->label(__('general.text')),
                Forms\Components\TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(1000000)
                    ->label(__('general.sort_order')),
                Forms\Components\Toggle::make('is_published')
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->inline(false)
                    ->label(__('general.published')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('text')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.text')),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.sort_order')),
                ToggleIconColumn::make('is_published')
                    ->onIcon('heroicon-s-eye')
                    ->offIcon('heroicon-o-eye-slash')
                    ->toggleable()
                    ->label(__('general.published')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Reference\Resources\RegistrationQuestionResource\Pages\ManageRegistrationQuestions::route('/'),
        ];
    }
}

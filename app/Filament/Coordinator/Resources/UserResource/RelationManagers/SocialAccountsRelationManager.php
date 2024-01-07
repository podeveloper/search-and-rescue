<?php

namespace App\Filament\Coordinator\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class SocialAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'socialAccounts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('platform')
                    ->options([
                        'facebook' => 'Facebook',
                        'instagram' => 'Instagram',
                        'twitter' => 'Twitter',
                        'telegram' => 'Telegram',
                    ]),
                Forms\Components\TextInput::make('user_id')->hidden()
                    ->extraInputAttributes(['value' => request()->user()->id]),
                Forms\Components\TextInput::make('username'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('platform')
            ->columns([
                Tables\Columns\TextColumn::make('platform')->searchable(),
                Tables\Columns\TextColumn::make('user.full_name')->searchable(),
                Tables\Columns\TextColumn::make('username')->searchable(),
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

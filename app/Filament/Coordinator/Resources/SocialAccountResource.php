<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Resources\SocialAccountResource\Pages;
use App\Filament\Resources\SocialAccountResource\RelationManagers;
use App\Models\SocialAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class SocialAccountResource extends Resource
{
    protected static ?string $model = SocialAccount::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('platform')
                    ->options([
                        'facebook' => 'Facebook',
                        'instagram' => 'Instagram',
                        'twitter' => 'Twitter',
                        'telegram' => 'Telegram',
                    ])
                    ->required()
                    ->string()
                    ->label(__('general.platform')),
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->string()

                    ->label(__('general.username')),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->exists('users','id')
                    ->label(__('general.user')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('platform')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.platform')),
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.username')),
                Tables\Columns\TextColumn::make('user.full_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.user')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Coordinator\Resources\SocialAccountResource\Pages\ManageSocialAccounts::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = strtolower(str_replace(' ', '_', self::getModelLabel())) . '_count';

        return Cache::rememberForever($cacheKey, function () {
            return self::getModel()::count();
        });
    }
}

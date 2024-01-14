<?php

namespace App\Filament\Reference\Resources;

use App\Filament\Reference\Resources\CertificateResource\Pages;
use App\Filament\Reference\Resources\CertificateResource\RelationManagers;
use App\Models\Certificate;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;

class CertificateResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Certificate::class;

    protected static ?string $navigationGroup = 'Reference Models';

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options([
                        'attendance' => 'Attendance Certificate',
                        'achievement' => 'Certificate of Achievement',
                        'other' => 'Other',
                    ]),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('text')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('users')
                    ->relationship('users', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('general.volunteers')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.event_volunteers_count')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificates::route('/'),
            'create' => Pages\CreateCertificate::route('/create'),
            'view' => Pages\ViewCertificate::route('/{record}'),
            'edit' => Pages\EditCertificate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = strtolower(str_replace(' ', '_', self::getModelLabel())) . '_count';

        return Cache::rememberForever($cacheKey, function () {
            return self::getModel()::count();
        });
    }
}

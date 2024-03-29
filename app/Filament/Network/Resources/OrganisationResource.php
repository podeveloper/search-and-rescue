<?php

namespace App\Filament\Network\Resources;

use App\Filament\Network\Resources;
use App\Filament\Resources\OrganisationResource\Pages;
use App\Filament\Resources\OrganisationResource\RelationManagers;
use App\Models\City;
use App\Models\Organisation;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class OrganisationResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = Organisation::class;

    protected static ?string $navigationGroup = 'Network';
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description', 'industry', 'phone', 'email', 'address', 'country.name', 'city.name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->columnSpanFull()
                    ->string()
                    ->label(__('general.name')),
                Forms\Components\MarkdownEditor::make('description')
                    ->label(__('general.description'))
                    ->columnSpan('full'),
                Forms\Components\TextInput::make('industry')
                    ->nullable()
                    ->string()
                    ->label(__('general.industry')),
                Forms\Components\TextInput::make('phone')
                    ->nullable()
                    ->tel()
                    ->maxLength(255)
                    ->label(__('general.phone')),
                Forms\Components\TextInput::make('email')
                    ->nullable()
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->nullable()
                    ->string()
                    ->label(__('general.full_address')),
                Forms\Components\TextInput::make('website')
                    ->nullable()
                    ->string(),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->nullable()
                    ->exists('countries', 'id')
                    ->label(__('general.country_singular')),
                Forms\Components\Select::make('city_id')
                    ->options(fn(Forms\Get $get): Collection => City::query()
                        ->where('country_id', $get('country_id'))
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->exists('cities', 'id')
                    ->label(__('general.city_singular')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.name')),
                Tables\Columns\TextColumn::make('description')
                    ->words(5)
                    ->wrap()
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.description')),
                Tables\Columns\TextColumn::make('industry')
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.industry')),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.phone')),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.email')),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable()
                    ->wrap()
                    ->label(__('general.full_address')),
                Tables\Columns\TextColumn::make('website')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.country_singular')),
                Tables\Columns\TextColumn::make('city.name')
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.city_singular')),
                Tables\Columns\TextColumn::make('contacts.full_name')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.people')),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('Y-m-d')
                    ->toggleable()
                    ->label(__('general.created_date')),
            ])
            ->paginated([10, 25, 50])
            ->defaultSort('name', 'asc')
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
                    //
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
            'index' => Resources\OrganisationResource\Pages\ListOrganisations::route('/'),
            'create' => Resources\OrganisationResource\Pages\CreateOrganisation::route('/create'),
            'edit' => Resources\OrganisationResource\Pages\EditOrganisation::route('/{record}/edit'),
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

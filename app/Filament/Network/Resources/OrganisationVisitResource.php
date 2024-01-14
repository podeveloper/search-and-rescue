<?php

namespace App\Filament\Network\Resources;

use App\Filament\Network\Resources;
use App\Filament\Resources\OrganisationVisitResource\Pages;
use App\Filament\Resources\OrganisationVisitResource\RelationManagers;
use App\Models\OrganisationVisit;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class OrganisationVisitResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = OrganisationVisit::class;

    protected static ?string $navigationGroup = 'Network';

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->default(now())
                    ->maxDate(now())
                    ->required()
                    ->label(__('general.date')),
                Forms\Components\Select::make('place_id')
                    ->relationship('place', 'name')
                    ->createOptionForm(fn(Form $form) => PlaceResource::form($form))
                    ->editOptionForm(fn(Form $form) => PlaceResource::form($form))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->label(__('general.place_singular'))
                    ->exists('places','id'),
                Forms\Components\Select::make('organisation_id')
                    ->relationship('organisation', 'name')
                    ->createOptionForm(fn(Form $form) => OrganisationResource::form($form))
                    ->editOptionForm(fn(Form $form) => OrganisationResource::form($form))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label(__('general.organisation_singular'))
                    ->exists('organisations','id'),
                Forms\Components\Select::make('host_id')
                    ->relationship('host', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label(__('general.host'))
                    ->exists('users','id'),
                Forms\Components\Textarea::make('explanation')
                    ->nullable()
                    ->columnSpanFull()
                    ->label(__('general.explanation')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.date')),
                Tables\Columns\TextColumn::make('place.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.place_singular')),
                Tables\Columns\TextColumn::make('organisation.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.organisation_singular')),
                Tables\Columns\TextColumn::make('host.full_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.host')),
                Tables\Columns\TextColumn::make('explanation')
                    ->words(5)
                    ->wrap()
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.explanation')),
            ])
            ->paginated([10, 25, 50])
            ->defaultSort('date','desc')
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

    public static function getPages(): array
    {
        return [
            'index' => Resources\OrganisationVisitResource\Pages\ManageOrganisationVisits::route('/'),
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

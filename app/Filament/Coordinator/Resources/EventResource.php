<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use App\Traits\NavigationLocalizationTrait;
use Archilex\ToggleIconColumn\Columns\ToggleIconColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class EventResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationGroup = 'Events';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 12;

    public static function getGloballySearchableAttributes(): array
    {
        return ['title','description','eventCategory.name','eventPlace.name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->string()
                    ->columnSpanFull()
                    ->label(__('general.title')),
                Forms\Components\RichEditor::make('description')
                    ->columnSpan('full')
                    ->nullable()
                    ->label(__('general.description')),
                Forms\Components\TextInput::make('location')
                    ->placeholder(__('general.paste_map_url_here'))
                    ->rule('url')
                    ->nullable()
                    ->string()
                    ->label(__('general.location_map_link')),
                Forms\Components\TextInput::make('capacity')
                    ->nullable()
                    ->numeric()
                    ->maxValue('500')
                    ->label(__('general.capacity')),
                Forms\Components\TextInput::make('organizer')
                    ->nullable()
                    ->string()
                    ->label(__('general.organizer')),
                Forms\Components\Select::make('responsibles')
                    ->relationship('responsibles', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('general.event_responsibles')),
                Forms\Components\DatePicker::make('date')
                    ->nullable()
                    ->label(__('general.date')),
                Forms\Components\TimePicker::make('starts_at')
                    ->seconds(false)
                    ->time()
                    ->label(__('general.starts_at')),
                Forms\Components\TimePicker::make('ends_at')
                    ->seconds(false)
                    ->time()
                    ->label(__('general.ends_at')),
                Forms\Components\Select::make('event_category_id')
                    ->relationship('eventCategory', 'name')
                    ->createOptionForm(fn(Form $form) => EventCategoryResource::form($form))
                    ->editOptionForm(fn(Form $form) => EventCategoryResource::form($form))
                    ->nullable()
                    ->exists('event_categories','id')
                    ->label(__('general.event_category_singular')),
                Forms\Components\Select::make('event_place_id')
                    ->relationship('eventPlace', 'name')
                    ->createOptionForm(fn(Form $form) => EventPlaceResource::form($form))
                    ->editOptionForm(fn(Form $form) => EventPlaceResource::form($form))
                    ->nullable()
                    ->exists('event_places','id')
                    ->label(__('general.event_place_singular')),
                Forms\Components\Select::make('users')
                    ->relationship('users', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('general.event_participants')),
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.title')),
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(function($state){

                        $state = str_replace('</p><p>', ' ', $state);
                        return strip_tags($state);
                    })
                    ->words(5)
                    ->wrap()
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.description')),
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->date('Y-m-d')
                    ->toggleable()
                    ->label(__('general.date')),
                Tables\Columns\TextColumn::make('starts_at')
                    ->sortable()
                    ->date('H:i')
                    ->toggleable()
                    ->label(__('general.starts_at')),
                Tables\Columns\TextColumn::make('ends_at')
                    ->sortable()
                    ->date('H:i')
                    ->toggleable()
                    ->label(__('general.ends_at')),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.location')),
                Tables\Columns\TextColumn::make('organizer')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.organizer')),
                Tables\Columns\TextColumn::make('responsibles.full_name')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.event_responsibles')),
                Tables\Columns\TextColumn::make('eventCategory.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.event_category_singular')),
                Tables\Columns\TextColumn::make('eventPlace.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.event_place_singular')),
                ToggleIconColumn::make('is_published')
                    ->onIcon('heroicon-s-eye')
                    ->offIcon('heroicon-o-eye-slash')
                    ->toggleable()
                    ->label(__('general.published')),
                Tables\Columns\TextColumn::make('capacity')->sortable()
                    ->toggleable()
                    ->label(__('general.capacity')),
                Tables\Columns\TextColumn::make('responsibles.full_name')
                    ->badge()
                    ->toggleable()
                    ->label(__('general.event_responsibles')),
                TextColumn::make('users.full_name')
                    ->badge()
                    ->label(__('general.participants')),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.event_participants_count')),
            ])
            ->paginated([10, 25, 50])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\Filter::make('title')
                    ->form([
                        Forms\Components\TextInput::make('title')
                            ->label(__('general.title')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $title = $data['title'];
                        return $title ? $query->where('title', 'like', '%' . $title . '%') : $query;
                    }),
                Tables\Filters\Filter::make('description')
                    ->form([
                        Forms\Components\TextInput::make('description')
                            ->label(__('general.description')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $description = $data['description'];
                        return $description ? $query->where('description', 'like', '%' . $description . '%') : $query;
                    }),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('eventCategory', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('general.event_category_singular')),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->label(__('general.date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $date = $data['date'];
                        return $date ? $query->where('date', '=', $date) : $query;
                    }),
                Tables\Filters\Filter::make('starts_at')
                    ->form([
                        Forms\Components\TimePicker::make('starts_at')->seconds(false)
                            ->label(__('general.starts_at')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $startsAt = $data['starts_at'];
                        return $startsAt ? $query->where('starts_at', '>=', $startsAt) : $query;
                    }),
                Tables\Filters\Filter::make('ends_at')
                    ->form([
                        Forms\Components\TimePicker::make('ends_at')->seconds(false)
                            ->label(__('general.ends_at')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $endsAt = $data['ends_at'];
                        return $endsAt ? $query->where('starts_at', '<=', $endsAt) : $query;
                    }),
                Tables\Filters\Filter::make('location')
                    ->form([
                        Forms\Components\TextInput::make('location')
                            ->placeholder('Paste here maps url.')
                            ->label(__('general.location')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $location = $data['location'];
                        return $location ? $query->where('location', 'like', '%' . $location . '%') : $query;
                    }),
                Tables\Filters\Filter::make('organizer')
                    ->form([
                        Forms\Components\TextInput::make('organizer')
                            ->label(__('general.organizer')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $organizer = $data['organizer'];
                        return $organizer ? $query->where('organizer', 'like', '%' . $organizer . '%') : $query;
                    }),
                Tables\Filters\SelectFilter::make('responsibles')
                    ->relationship('responsibles', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('general.event_responsibles')),
                Tables\Filters\Filter::make('capacity')
                    ->form([
                        Forms\Components\TextInput::make('capacity')->numeric()
                            ->label(__('general.capacity')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $capacity = $data['capacity'];
                        return $capacity ? $query->where('capacity', '=', $capacity) : $query;
                    }),
                Tables\Filters\Filter::make('is_published')
                    ->label(__('general.published'))
                    ->query(fn(Builder $query): Builder => $query->where('is_published', true)),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
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
            'index' => \App\Filament\Coordinator\Resources\EventResource\Pages\ListEvents::route('/'),
            'create' => \App\Filament\Coordinator\Resources\EventResource\Pages\CreateEvent::route('/create'),
            'edit' => \App\Filament\Coordinator\Resources\EventResource\Pages\EditEvent::route('/{record}/edit'),
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

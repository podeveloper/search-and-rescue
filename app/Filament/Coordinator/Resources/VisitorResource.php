<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Resources\VisitorResource\Pages;
use App\Filament\Resources\VisitorResource\RelationManagers;
use App\Models\Visitor;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class VisitorResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = Visitor::class;

    protected static ?string $navigationGroup = 'Places';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?int $navigationSort = 14;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'surname', 'full_name', 'email', 'phone'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('profile_photo')
                    ->columnSpan(2)
                    ->image()
                    ->maxSize(10000)
                    ->label(__('general.profile_photo')),
                Forms\Components\TextInput::make('name')
                    ->nullable()
                    ->string()
                    ->label(__('general.name')),
                Forms\Components\TextInput::make('surname')
                    ->nullable()
                    ->string()
                    ->label(__('general.surname')),
                Forms\Components\Select::make('gender_id')
                    ->relationship('gender', 'name')
                    ->nullable()
                    ->exists('genders','id')
                    ->label(__('gender.singular')),
                Forms\Components\Select::make('nationality_id')
                    ->relationship('nationality', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->exists('nationalities','id')
                    ->label(__('nationality.singular')),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->exists('countries','id')
                    ->label(__('country.singular')),
                Forms\Components\Select::make('language_id')
                    ->relationship('language', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->exists('languages','id')
                    ->label(__('language.singular')),
                Forms\Components\Select::make('companion_id')
                    ->relationship('companion', 'full_name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->exists('users','id')
                    ->label(__('general.companion')),
                Forms\Components\TextInput::make('phone')
                    ->nullable()
                    ->tel()
                    ->maxLength(255)
                    ->label(__('general.phone')),
                Forms\Components\TextInput::make('email')
                    ->nullable()
                    ->email()
                    ->maxLength(255)
                    ->label(__('general.email')),
                Forms\Components\TextInput::make('facebook')
                    ->nullable()
                    ->string(),
                Forms\Components\TextInput::make('twitter')
                    ->nullable()
                    ->string(),
                Forms\Components\TextInput::make('instagram')
                    ->nullable()
                    ->string(),
                Forms\Components\TextInput::make('telegram')
                    ->nullable()
                    ->string(),
                Forms\Components\Select::make('occupation_id')
                    ->relationship('occupation', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->exists('occupations','id')
                    ->label(__('occupation.singular')),
                Forms\Components\TextInput::make('occupation_text')
                    ->nullable()
                    ->string(),
                Forms\Components\Select::make('organisation_id')
                    ->relationship('organisation', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->exists('organisations','id')
                    ->label(__('organisation.singular')),
                Forms\Components\TextInput::make('organisation_text')
                    ->nullable()
                    ->string()
                    ->label(__('general.date')),
                Forms\Components\MarkdownEditor::make('explanation')
                    ->columnSpan('full')
                    ->nullable()
                    ->maxLength(300)
                    ->label(__('general.explanation')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group_number')
                    ->sortable()
                    ->toggleable()
                    ->label(__('visitor.group_number')),
                Tables\Columns\TextColumn::make('country.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('country.singular')),
                Tables\Columns\TextColumn::make('language.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('language.singular')),
                Tables\Columns\TextColumn::make('companion.full_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.companion')),
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->width(50)
                    ->height(50)
                    ->circular()
                    ->toggleable()
                    ->label(__('general.profile_photo')),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.name')),
                Tables\Columns\TextColumn::make('surname')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.surname')),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.full_name')),
                Tables\Columns\TextColumn::make('gender.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('gender.singular')),
                Tables\Columns\TextColumn::make('nationality.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('nationality.singular')),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone number copied')
                    ->copyMessageDuration(1500)
                    ->toggleable()
                    ->label(__('general.phone')),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500)
                    ->toggleable()
                    ->label(__('general.email')),
                Tables\Columns\TextColumn::make('facebook')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('twitter')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('instagram')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('telegram')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('occupation.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('occupation.singular')),
                Tables\Columns\TextColumn::make('organisation.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('organisation.singular')),
                Tables\Columns\TextColumn::make('explanation')
                    ->words(5)
                    ->wrap()
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.explanation')),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('group_number','desc')
            ->filters([
                Tables\Filters\Filter::make('group_number')
                    ->form([
                        Forms\Components\TextInput::make('group_number')->numeric()
                            ->label(__('visitor.group_number')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $groupNumber = $data['group_number'];
                        return $groupNumber ? $query->where('capacity', '=', $groupNumber) : $query;
                    }),
                Tables\Filters\SelectFilter::make('country')
                    ->relationship('country', 'name')
                    ->preload()
                    ->multiple()
                    ->searchable()
                    ->label(__('country.singular')),
                Tables\Filters\SelectFilter::make('language')
                    ->relationship('language', 'name')
                    ->preload()
                    ->multiple()
                    ->searchable()
                    ->label(__('language.singular')),
                Tables\Filters\Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label(__('general.name')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $name = $data['name'];
                        return $name ? $query->where('name', 'like', '%' . $name . '%') : $query;
                    }),
                Tables\Filters\Filter::make('surname')
                    ->form([
                        Forms\Components\TextInput::make('surname')
                            ->label(__('general.surname')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $surname = $data['surname'];
                        return $surname ? $query->where('surname', 'like', '%' . $surname . '%') : $query;
                    }),
                Tables\Filters\SelectFilter::make('gender')
                    ->relationship('gender', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('gender.singular')),
                Tables\Filters\SelectFilter::make('nationality')
                    ->relationship('nationality', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('nationality.singular')),
                Tables\Filters\Filter::make('phone')
                    ->form([
                        Forms\Components\TextInput::make('phone')
                            ->label(__('general.phone')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $phone = $data['phone'];
                        return $phone ? $query->where('phone', 'like', '%' . $phone . '%') : $query;
                    }),
                Tables\Filters\Filter::make('email')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label(__('general.email')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $email = $data['email'];
                        return $email ? $query->where('email', 'like', '%' . $email . '%') : $query;
                    }),
                Tables\Filters\SelectFilter::make('occupation')
                    ->relationship('occupation', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('occupation.singular')),
                Tables\Filters\SelectFilter::make('organisation')
                    ->relationship('organisation', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('organisation.singular')),
                Tables\Filters\Filter::make('explanation')
                    ->form([
                        Forms\Components\TextInput::make('explanation')
                            ->label(__('general.explanation')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $explanation = $data['explanation'];
                        return $explanation ? $query->where('explanation', 'like', '%' . $explanation . '%') : $query;
                    }),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)

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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Coordinator\Resources\VisitorResource\Pages\ListVisitors::route('/'),
            'create' => \App\Filament\Coordinator\Resources\VisitorResource\Pages\CreateVisitor::route('/create'),
            'edit' => \App\Filament\Coordinator\Resources\VisitorResource\Pages\EditVisitor::route('/{record}/edit'),
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

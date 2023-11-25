<?php

namespace App\Filament\Network\Resources;

use App\Filament\Network\Resources;
use App\Filament\Reference\Resources\OccupationResource;
use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationGroup = 'Network';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'email', 'phone','address','gender.name','nationality.name','organisation.name','organisation_text','occupation.name','occupation_text','contactCategory.name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->required()
                    ->string(),
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
                    ->maxLength(300),
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
                Forms\Components\Select::make('organisation_id')
                    ->relationship('organisation', 'name')
                    ->createOptionForm(fn(Form $form) => OrganisationResource::form($form))
                    ->editOptionForm(fn(Form $form) => OrganisationResource::form($form))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->exists('organisations','id')
                    ->label(__('organisation.singular')),
                Forms\Components\TextInput::make('organisation_text')
                    ->nullable()
                    ->string(),
                Forms\Components\Select::make('occupation_id')
                    ->relationship('occupation', 'name')
                    ->createOptionForm(fn(Form $form) => OccupationResource::form($form))
                    ->editOptionForm(fn(Form $form) => OccupationResource::form($form))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->exists('occupations','id')
                    ->label(__('occupation.singular')),
                Forms\Components\TextInput::make('occupation_text')
                    ->nullable()
                    ->string(),
                Forms\Components\MarkdownEditor::make('explanation')
                    ->columnSpan('full')
                    ->nullable()
                    ->maxLength(300),
                Forms\Components\Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('contact_category_id')
                    ->relationship('contactCategory', 'name')
                    ->createOptionForm(fn(Form $form) => ContactCategoryResource::form($form))
                    ->editOptionForm(fn(Form $form) => ContactCategoryResource::form($form))
                    ->nullable()
                    ->exists('contact_categories','id')
                    ->label(__('general.category')),
     ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('gender.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nationality.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('organisation.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('organisation_text')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('occupation.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('occupation_text')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('explanation')
                    ->words(5)
                    ->wrap()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tags.name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('contactCategory.name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->date('Y-m-d')
                    ->toggleable(),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('full_name','asc')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Resources\ContactResource\Pages\ListContacts::route('/'),
            'create' => Resources\ContactResource\Pages\CreateContact::route('/create'),
            'edit' => Resources\ContactResource\Pages\EditContact::route('/{record}/edit'),
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

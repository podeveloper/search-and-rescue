<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Resources\TodoResource\Pages;
use App\Filament\Resources\TodoResource\RelationManagers;
use App\Models\Todo;
use App\Traits\NavigationLocalizationTrait;
use Archilex\ToggleIconColumn\Columns\ToggleIconColumn;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class TodoResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = Todo::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Todo';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['title','content','category.name','assignors.name','responsibles.name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->columns(2)
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->relationship(name: 'category', titleAttribute: 'name')
                            ->createOptionForm(fn(Form $form) => TodoResource::form($form))
                            ->editOptionForm(fn(Form $form) => TodoResource::form($form))
                            ->columnSpan('full')
                            ->nullable()
                            ->exists('todo_categories','id')
                            ->label(__('general.category')),
                        Forms\Components\Select::make('assignors')
                            ->relationship('assignors', 'full_name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->loadingMessage('Loading users...')
                            ->label(__('todo.assignors')),
                        Forms\Components\Select::make('responsibles')
                            ->relationship('responsibles', 'full_name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(__('todo.responsibles')),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->columnSpan('full')
                            ->label(__('general.title')),
                        Forms\Components\RichEditor::make('content')
                            ->columnSpanFull()
                            ->nullable()
                            ->maxLength(300)
                            ->label(__('general.content')),
                        Forms\Components\FileUpload::make('image')
                            ->columnSpanFull()
                            ->disk('public')
                            ->directory('filament-todo-images')
                            ->visibility('public')
                            ->image()
                            ->imagePreviewHeight('250')
                            // ->imageResizeMode('cover')
                            // ->imageCropAspectRatio('16:9')
                            // ->imageResizeTargetWidth('1920')
                            // ->imageResizeTargetHeight('1080')
                            ->imageEditor()
                            // ->imageEditorMode(1)
                            // ->imageEditorViewportWidth('1920')
                            // ->imageEditorViewportHeight('1080')
                            ->imageEditorAspectRatios([
                                null,
                                '1:1',
                                '4:3',
                                '16:9',
                            ])
                            ->label(__('general.image')),
                        Section::make(__('general.status'))->columns(2)
                            ->schema([
                                Forms\Components\DatePicker::make('created_at')
                                    ->label('Given Date')
                                    ->format('Y/m/d')
                                    ->default(now()->toDateString())
                                    ->required()
                                    ->label(__('todo.given_date')),
                                Forms\Components\DatePicker::make('deadline_at')
                                    ->format('Y/m/d')
                                    ->required()
                                    ->label(__('todo.deadline_date')),
                                Forms\Components\Toggle::make('is_finished')
                                    ->label(__('general.finished')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('todo_category.singular')),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.title')),
                Tables\Columns\TextColumn::make('content')
                    ->words(5)
                    ->wrap()
                    ->html()
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.content')),
                Tables\Columns\TextColumn::make('assignors.full_name')
                    ->badge()
                    ->toggleable()
                    ->label(__('todo.assignors')),
                Tables\Columns\TextColumn::make('responsibles.full_name')
                    ->badge()
                    ->toggleable()
                    ->label(__('todo.responsibles')),
                Tables\Columns\ImageColumn::make('image')
                    ->toggleable()
                    ->label(__('general.image')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Given Date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable()
                    ->label(__('todo.given_date')),
                Tables\Columns\TextColumn::make('deadline_at')
                    ->sortable()
                    ->toggleable()
                    ->label(__('todo.deadline_date')),
                ToggleIconColumn::make('is_finished')
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-o-x-mark')
                    ->toggleable()
                    ->label(__('general.finished'))
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('deadline_at','asc')
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
                Tables\Filters\Filter::make('content')
                    ->form([
                        Forms\Components\TextInput::make('content')
                            ->label(__('general.content')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $content = $data['content'];
                        return $content ? $query->where('content', 'like', '%' . $content . '%') : $query;
                    }),
                Tables\Filters\SelectFilter::make('assignors')
                    ->relationship('assignors', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('todo.assignors')),
                Tables\Filters\SelectFilter::make('responsibles')
                    ->relationship('responsibles', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('todo.responsibles')),
                Tables\Filters\Filter::make('deadline_until')
                    ->form([
                        Forms\Components\DatePicker::make('deadline_until')
                            ->displayFormat('Y-m-d')
                            ->label(__('todo.deadline_date'))
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $deadlineAt = $data['deadline_until'];
                        return $deadlineAt ? $query->where('deadline_at', '>=', $deadlineAt) : $query;
                    }),
                Tables\Filters\Filter::make('unfinished_only')
                    ->label(__('general.unfinished'))
                    ->query(fn (Builder $query): Builder => $query->where('is_finished', false)),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
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
            'index' => \App\Filament\Coordinator\Resources\TodoResource\Pages\ListTodos::route('/'),
            'create' => \App\Filament\Coordinator\Resources\TodoResource\Pages\CreateTodo::route('/create'),
            'edit' => \App\Filament\Coordinator\Resources\TodoResource\Pages\EditTodo::route('/{record}/edit'),
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

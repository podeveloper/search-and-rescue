<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\VehicleModel;
use Carbon\Carbon;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class MyEventsTableWidget extends BaseWidget
{
    protected function getTableHeading(): string|Htmlable|null
    {
        return __('general.my_events');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::whereHas('users', function ($query) {
                    $query->where('user_id', auth()->user()->id);
                })
                    ->orderBy('date', 'desc')
            )
            ->columns([
                Split::make([
                    TextColumn::make('date')
                        ->weight(FontWeight::Bold)
                        ->toggleable()
                        ->label(__('general.date')),
                    TextColumn::make('title')
                        ->weight(FontWeight::Bold)
                        ->toggleable()
                        ->label(__('general.title')),
                ]),
                Panel::make([
                    Split::make([
                        TextColumn::make('category_label')
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default(__('general.event_category_singular')),
                        TextColumn::make('eventCategory.name')
                            ->badge()
                            ->icon('heroicon-o-rectangle-stack')
                            ->extraAttributes(['class' => 'py-2'])
                            ->weight(FontWeight::Bold),
                    ]),
                    Split::make([
                        TextColumn::make('start_time_label')
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default(__('general.starts_at')),
                        TextColumn::make('time')
                            ->default(fn(Event $record) => new HtmlString(Carbon::parse($record->starts_at)->format('H:i')))
                            ->badge()
                            ->icon('heroicon-o-clock')
                            ->color('info')
                            ->weight(FontWeight::Bold)
                            ->label(__('general.time')),
                    ]),
                    Split::make([
                        TextColumn::make('end_time_label')
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default(__('general.ends_at')),
                        TextColumn::make('time')
                            ->default(fn(Event $record) => new HtmlString(Carbon::parse($record->ends_at)->format('H:i')))
                            ->badge()
                            ->icon('heroicon-o-clock')
                            ->color('info')
                            ->weight(FontWeight::Bold)
                            ->label(__('general.time')),
                    ]),
                    Split::make([
                        TextColumn::make('organizer_label')
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default(__('general.organizer')),
                        TextColumn::make('organizer')
                            ->badge()
                            ->icon('heroicon-o-user')
                            ->color('success')
                            ->default(fn(Event $record) => $record->organizer ?? 'MAKUD'),
                    ]),
                    Split::make([
                        TextColumn::make('responsibles_label')
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default(__('general.event_responsibles')),
                        TextColumn::make('responsibles.full_name')
                            ->badge()
                            ->icon('heroicon-o-users')
                            ->toggleable()
                            ->label(__('general.event_responsibles')),
                    ]),
                    Split::make([
                        TextColumn::make('capacity_label')
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default(__('general.capacity')),
                        TextColumn::make('capacity')
                            ->badge()
                            ->icon('heroicon-o-user-plus')
                            ->color('success')
                            ->default(fn(Event $record) => $record->capacity),
                    ]),
                    Split::make([
                        TextColumn::make('description_label')
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default(__('general.description')),
                        TextColumn::make('description')
                            ->badge()
                            ->color('success')
                            ->formatStateUsing(fn(Event $record) => new HtmlString($record->description)),
                    ]),
                ])
                    ->collapsed(false)
                    ->collapsible(),
            ])
            ->paginated([10, 25, 50])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ], position: ActionsPosition::BeforeColumns)
            ->emptyStateHeading(__('general.table_empty_state_heading'))
            ->emptyStateDescription('');
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Country;
use App\Models\Event;
use App\Models\Gender;
use App\Models\Language;
use App\Models\Nationality;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\HtmlString;

class IncomingEventsWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::where('date', '>=', now()->toDateString())
                    ->orderBy('date', 'asc')
                    ->orderBy('starts_at', 'asc')
            )
            ->columns([
                TextColumn::make('title')
                    ->extraAttributes(['class' => 'py-2'])
                    ->weight(FontWeight::Bold)
                    ->label(__('general.title')),
                Split::make([
                    TextColumn::make('date_text')
                        ->default(fn(Event $record) => $record->date ? $record->date . ' ' .  __('general.'.strtolower(Carbon::parse($record->date)->format('l'))) : '')
                        ->extraAttributes(['class' => 'py-2'])
                        ->badge()
                        ->color('info')
                        ->weight(FontWeight::Bold)
                        ->label(__('general.date')),
                    TextColumn::make('eventCategory.name')
                        ->badge()
                        ->icon('heroicon-o-rectangle-stack')
                        ->alignCenter()
                        ->extraAttributes(['class' => 'py-2'])
                        ->weight(FontWeight::Bold),
                ]),
                Panel::make([
                    Split::make([
                        TextColumn::make('start_time_label')
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default(__('general.starts_at')),
                        TextColumn::make('time')
                            ->default(fn(Event $record) => new HtmlString(Carbon::parse($record->starts_at)->format('H:i') . ($record->ends_at ? ' - ' . Carbon::parse($record->ends_at)->format('H:i') : '')))
                            ->badge()
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
                            ->default(fn(Event $record) => new HtmlString(Carbon::parse($record->ends_at)->format('H:i') . ($record->ends_at ? ' - ' . Carbon::parse($record->ends_at)->format('H:i') : '')))
                            ->badge()
                            ->color('info')
                            ->weight(FontWeight::Bold)
                            ->label(__('general.time')),
                    ]),
                    Split::make([
                        TextColumn::make('location_label')
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default(__('general.location')),
                        TextColumn::make('location')
                            ->default(fn(Event $record) => $record->eventPlace->name ?? 'MAKUD Merkez', true)
                            ->url(fn(Event $record) => $record->location ?? 'https://maps.app.goo.gl/aQigLAEPpw8WdRe99', true)
                            ->badge()
                            ->color('success')
                            ->weight(FontWeight::Bold),
                    ]),
                    Split::make([
                        TextColumn::make('organizer_label')
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default(__('general.organizer')),
                        TextColumn::make('organizer')
                            ->badge()
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
                            ->toggleable()
                            ->label(__('general.event_responsibles')),
                    ]),
                    Split::make([
                        TextColumn::make('label')
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default(__('general.participants')),
                        TextColumn::make('users.full_name')
                            ->badge()
                            ->label(__('general.participants')),
                    ]),
                    Split::make([
                        TextColumn::make('label')
                            ->weight(FontWeight::Bold)
                            ->default('Description:'),
                        TextColumn::make('description')
                            ->formatStateUsing(fn(Event $record) => new HtmlString($record->description)),
                    ]),
                ])
                    ->collapsible(),
            ])
            ->paginated([10, 25, 50])
            ->headerActions([
                //
            ])
            ->actions([
                Action::make('Join')
                    ->requiresConfirmation()
                    ->label(__('general.join'))
                    ->color('success')
                    ->icon('heroicon-m-cursor-arrow-ripple')
                    ->iconPosition(IconPosition::After)
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->action(function (array $data, Event $record) {
                        $record->users()->attach(auth()->user()->id);
                    })
                    ->visible(fn(Event $record) => auth()?->user() != null && !$record->users->contains(auth()?->user()?->id)),
                Action::make('Leave')
                    ->requiresConfirmation()
                    ->label(__('general.leave'))
                    ->color('danger')
                    ->icon('heroicon-m-arrow-right-on-rectangle')
                    ->iconPosition(IconPosition::After)
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->action(function (array $data, Event $record) {
                        $record->users()->detach(auth()->user()->id);
                    })
                    ->visible(fn(Event $record) => auth()?->user() != null && $record->users->contains(auth()?->user()?->id)),
            ], position: ActionsPosition::BeforeColumns)
            ->paginated(false)
            ->defaultGroup('eventPlace.name')
            ->groups([
                Group::make('eventPlace.name')
                    ->label('')
                    ->collapsible(),
            ]);
    }
}

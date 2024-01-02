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
                Event::where('date',request('date') ?? now()->toDateString())
                    ->orderBy('date','desc')
                    ->orderBy('starts_at','asc')
            )
            ->columns([
                Split::make([
                    TextColumn::make('title')
                        ->extraAttributes(['style' => 'margin-bottom: 20px'])
                        ->weight(FontWeight::Bold)
                        ->label(__('general.title')),
                ]),
                Split::make([
                    TextColumn::make('time')
                        ->default(fn(Event $record) => new HtmlString(Carbon::parse($record->starts_at)->format('H:i') . ($record->ends_at ? ' - ' . Carbon::parse($record->ends_at)->format('H:i') : '')))
                        ->badge()
                        ->icon('heroicon-o-clock')
                        ->weight(FontWeight::Bold)
                        ->label(__('general.time')),
                    TextColumn::make('location')
                        ->icon('heroicon-o-map-pin')
                        ->default('Location')
                        ->url(fn(Event $record) => $record->location ? $record->location : 'https://maps.app.goo.gl/uSHFkVEHniiGuhVq8',true)
                        ->badge()
                        ->color('info')
                        ->weight(FontWeight::Bold),
                ]),
                Panel::make([
                    Stack::make([
                        TextColumn::make('organizer')
                            ->weight(FontWeight::Bold)
                            ->visible(fn()=> auth()?->user() != null)
                            ->formatStateUsing(fn(Event $record) => 'Organizer: ' . $record->organizer),
                        TextColumn::make('Empty')
                            ->visible(fn()=> auth()?->user() != null)
                            ->default(new HtmlString('&nbsp;')),
                        TextColumn::make('label')
                            ->visible(fn()=> auth()?->user() != null)
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default('Volunteers:'),
                        TextColumn::make('users.full_name')
                            ->visible(fn()=> auth()?->user() != null)
                            ->badge()
                            ->label(__('general.volunteers')),
                        TextColumn::make('Empty')
                            ->visible(fn()=> auth()?->user() != null)
                            ->default(new HtmlString('&nbsp;')),
                        TextColumn::make('label')
                            ->weight(FontWeight::Bold)
                            ->extraAttributes(['class' => 'py-2'])
                            ->default('Visitors:'),
                        TextColumn::make('label')
                            ->badge()
                            ->color('success')
                            ->separator(',')
                            ->default(function (Event $record) {

                                if (auth()->check()) {
                                        $result = "";
                                        foreach ($record->visitors as $visitor)
                                        {
                                            // Code for authenticated users
                                            $result .= "Fullname: {$visitor->full_name}<br>";
                                            $result .= "Gender: {$visitor->gender?->name}<br>";
                                            $result .= "Nationality: {$visitor->nationality?->name}<br>";
                                            $result .= "Country: {$visitor->country?->name}<br>";
                                            $result .= "Invited By: " . ($visitor->companion ? $visitor->companion?->full_name : 'Unknown') . "<br>";
                                            $result .= "Explanation: {$visitor->explanation}<br>";
                                            $result .= "<br>";
                                        }

                                        $result = new HtmlString($result);
                                        return $result;
                                    } else {
                                        $result = "";
                                        foreach ($record->visitors as $visitor)
                                        {
                                            $result .= "({$visitor->country->name}),";
                                        }
                                        return $result;
                                    }
                            })
                            ->label(__('general.volunteers')),
                        TextColumn::make('Empty')
                            ->default(new HtmlString('&nbsp;')),
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
                Action::make('add_visitor')
                    ->label(fn()=> auth()?->user() != null ? __('general.add_visitor') : __('general.join'))
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->icon('heroicon-m-user-plus')
                    ->color('info')
                    ->form([
                        Repeater::make('visitors')
                            ->label(__('general.visitor_plural'))
                            ->schema([
                                TextInput::make('full_name')
                                    ->label(__('general.full_name'))
                                    ->required(),
                                TextInput::make('phone')
                                    ->label(__('general.phone'))
                                    ->numeric()
                                    ->requiredIf('email', null),
                                TextInput::make('email')
                                    ->label(__('general.email'))
                                    ->requiredIf('phone', null),
                                Select::make('gender_id')
                                    ->label(__('general.gender_singular'))
                                    ->required()
                                    ->options(Gender::get(['name', 'id'])->pluck('name','id')),
                                Select::make('nationality')
                                    ->searchable()
                                    ->label(__('general.nationality_singular'))
                                    ->required()
                                    ->options(Nationality::get(['name', 'id'])->pluck('name','id')),
                                Select::make('country_id')
                                    ->searchable()
                                    ->label(__('general.country_singular'))
                                    ->required()
                                    ->options(Country::get(['name', 'id'])->pluck('name','id'))
                                    ->searchable(),
                                Select::make('language_id')
                                    ->options(Language::pluck('name','id'))
                                    ->required()
                                    ->searchable()
                                    ->exists('languages','id')
                                    ->label(__('general.language_singular')),
                                Textarea::make('explanation')
                                    ->label(__('general.explanation'))
                                    ->visible(auth()?->user() != null)
                            ])->addActionLabel(__('general.add_one_more'))
                    ])
                    ->action(function (array $data, Event $record){
                        $record->addVisitors($data['visitors']);
                    }),
                Action::make('Join')
                    ->requiresConfirmation()
                    ->label(__('general.join'))
                    ->color('success')
                    ->icon('heroicon-m-cursor-arrow-ripple')
                    ->iconPosition(IconPosition::After)
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->action(function (array $data, Event $record){
                        $record->users()->attach(auth()->user()->id);
                    })
                    ->visible(fn(Event $record)=> auth()?->user() != null && !$record->users->contains(auth()?->user()?->id)),
                Action::make('Leave')
                    ->requiresConfirmation()
                    ->label(__('general.leave'))
                    ->color('danger')
                    ->icon('heroicon-m-arrow-right-on-rectangle')
                    ->iconPosition(IconPosition::After)
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->action(function (array $data, Event $record){
                        $record->users()->detach(auth()->user()->id);
                    })
                    ->visible(fn(Event $record)=> auth()?->user() != null && $record->users->contains(auth()?->user()?->id)),
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

<?php

namespace App\Filament\Coordinator\Widgets;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class EventCalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Event::class;

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */

    public function fetchEvents(array $fetchInfo): array
    {
        $startDate = Carbon::parse($fetchInfo['start']);
        $endDate = Carbon::parse($fetchInfo['end']);

        $query = Event::query()
            ->published()
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->map(
                fn (Event $event) => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'date' => $event->date,
                    'start' => $event->date . ' ' . $event->starts_at,
                    'end' => $event->date . ' ' . $event->ends_at,
                    'location' => $event->location,
                    'capacity' => $event->capacity,
                    'organizer' => $event->organizer,
                    'is_published' => $event->is_published,
                    'event_category_id' => $event->eventCategory?->id,
                    'event_place_id' => $event->eventPlace?->id,
                ]
            )
            ->all();

        return $query;
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mountUsing(
                    function (Form $form, array $arguments) {
                        $date = array_key_exists('start', $arguments) && $arguments['start'] ? Carbon::parse($arguments['start'])->toDateString() : Carbon::now()->toDateString();
                        $form->fill([
                            'date' => $date,
                            'starts_at' => $arguments['start'] ?? null,
                            'ends_at' => $arguments['end'] ?? null,
                            'is_published' => true,
                        ]);
                    }
                )
        ];
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(
                    function (Event $record, Form $form, array $arguments) {
                        $form->fill([
                            'title' => $arguments['event']['title'] ?? $record->title,
                            'description' => $arguments['event']['description'] ?? $record->description,
                            'date' => $arguments['event']['date'] ?? $record->date,
                            'starts_at' => $arguments['event']['start'] ?? $record->starts_at,
                            'ends_at' => $arguments['event']['end'] ?? $record->ends_at,
                            'location' => $arguments['event']['location'] ?? $record->location,
                            'capacity' => $arguments['event']['capacity'] ?? $record->capacity,
                            'organizer' => $arguments['event']['organizer'] ?? $record->organizer,
                            'is_published' => $arguments['event']['is_published'] ?? $record->is_published,
                            'event_category_id' => $arguments['event']['event_category_id'] ?? $record->event_category_id,
                            'event_place_id' => $arguments['event']['event_place_id'] ?? $record->event_place_id,
                        ]);
                    }
                ),
            DeleteAction::make(),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('title')
                        ->label(__('general.title')),
                    TextInput::make('description')
                        ->label(__('general.description')),
                    TextInput::make('location')
                        ->label(__('general.location')),
                    TextInput::make('capacity')
                        ->numeric()->
                        maxValue(500)
                        ->label(__('general.capacity')),
                    TextInput::make('organizer')
                        ->label(__('general.organizer')),
                    DatePicker::make('date')
                        ->label(__('general.date')),
                    TimePicker::make('starts_at')
                        ->seconds(false)
                        ->label(__('general.starts_at')),
                    TimePicker::make('ends_at')
                        ->seconds(false)
                        ->label(__('general.ends_at')),
                    Select::make('event_category_id')
                        ->relationship('eventCategory', 'name')
                        ->label(__('event_category.singular')),
                    Select::make('event_place_id')
                        ->relationship('eventPlace', 'name')
                        ->label(__('event_place.singular')),
                    Select::make('users')
                        ->relationship('users', 'full_name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->label(__('event.participants')),
                    Toggle::make('is_published')
                        ->onIcon('heroicon-m-check')
                        ->offIcon('heroicon-m-x-mark')
                        ->inline(false)
                        ->label(__('general.published')),
                ]),
        ];
    }

    public function onEventDrop(array $event, array $oldEvent, array $relatedEvents, array $delta): bool
    {
        if ($this->getModel()) {
            $this->record = $this->resolveRecord($event['id']);
        }

        $date = Carbon::parse($event["start"])->toDateString();
        $this->record->date = $date;
        $this->mountAction('edit', [
            'type' => 'drop',
            'event' => $event,
            'oldEvent' => $oldEvent,
            'relatedEvents' => $relatedEvents,
            'delta' => $delta,
        ]);

        return false;
    }
}

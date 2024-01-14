<?php

namespace App\Filament\Coordinator\Widgets;

use App\Models\Todo;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class TodoCalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Todo::class;

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */

    public function fetchEvents(array $fetchInfo): array
    {
        $startDate = Carbon::parse($fetchInfo['start']);
        $endDate = Carbon::parse($fetchInfo['end'])->endOfMonth();

        $query = Todo::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(
                fn (Todo $todo) => [
                    'id' => $todo->id,
                    'title' => $todo->title,
                    'content' => $todo->content,
                    'date' => Carbon::parse($todo->created_at)->toDateString(),
                    'start' => Carbon::parse($todo->created_at),
                    'end' => Carbon::parse($todo->deadline_at)->addDay(), // We add day if we want to include deadline day
                    'category_id' => $todo->category?->id,
                    'is_finished' => $todo->is_finished,
                    'deadline_at' => $todo->deadline_at,
                ]
            )
            ->all();

        return $query;
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('general.create_todo'))
                ->mountUsing(
                    function (Form $form, array $arguments) {
                        $form->fill([
                            'title' => $arguments['title'] ?? null,
                            'content' => $arguments['content'] ?? null,
                            'category_id' => $arguments['category_id'] ?? null,
                            'created_at' => $arguments['created_at'] ?? null,
                            'deadline_at' => $arguments['deadline_at'] ?? null,
                            'is_finished' => $arguments['is_finished'] ?? null,
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
                    function (Todo $record, Form $form, array $arguments) {
                        $form->fill([
                            'title' => $arguments['event']['title'] ?? $record->title,
                            'content' => $arguments['event']['content'] ?? $record->content,
                            'category_id' => $arguments['event']['category_id'] ?? $record->category_id,
                            'created_at' => $arguments['event']['created_at'] ?? $record->created_at,
                            'deadline_at' => $arguments['event']['deadline_at'] ?? $record->deadline_at,
                            'is_finished' => $arguments['event']['is_finished'] ?? $record->is_finished,
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
                        ->columnSpanFull()
                        ->label(__('general.title')),
                    MarkdownEditor::make('content')
                        ->columnSpan('full')
                        ->label(__('general.content')),
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->columnSpan('full')
                        ->label(__('todo_category.singular')),
                    Select::make('assignors')
                        ->relationship('assignors', 'full_name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->label(__('todo.assignors')),
                    Select::make('responsibles')
                        ->relationship('responsibles', 'full_name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->label(__('todo.responsibles')),
                    DateTimePicker::make('created_at')
                        ->label(__('general.created_date')),
                    DateTimePicker::make('deadline_at')
                        ->label(__('todo.deadline_date')),
                    Toggle::make('is_finished')
                        ->onIcon('heroicon-m-check')
                        ->offIcon('heroicon-m-x-mark')
                        ->inline(false)
                        ->label(__('general.finished')),
                ]),
        ];
    }

    public function onEventDrop(array $todo, array $oldEvent, array $relatedEvents, array $delta): bool
    {
        return false;
        if ($this->getModel()) {
            $this->record = $this->resolveRecord($todo['id']);
        }

        $date = Carbon::parse($todo["start"])->toDateString();
        $this->record->date = $date;
        $this->mountAction('edit', [
            'type' => 'drop',
            'event' => $todo,
            'oldEvent' => $oldEvent,
            'relatedEvents' => $relatedEvents,
            'delta' => $delta,
        ]);

        return false;
    }
}

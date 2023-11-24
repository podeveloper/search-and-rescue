<?php

namespace App\Jobs;

use App\Models\Todo;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTodoNotificationToResponsibles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $todo;

    /**
     * Create a new job instance.
     */
    public function __construct(Todo $todo)
    {
        $this->todo = $todo;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Notification::make()
            ->title('You have a new todo: ' . $this->todo->title .'.')
            ->icon('heroicon-o-document-check')
            ->sendToDatabase($this->todo->responsibles);

    }
}

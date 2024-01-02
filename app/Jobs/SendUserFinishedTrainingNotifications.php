<?php

namespace App\Jobs;

use App\Events\UserFinishedTraining;
use App\Models\Training;
use App\Models\User;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendUserFinishedTrainingNotifications
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $training;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, Training $training)
    {
        $this->user = $user;
        $this->training = $training;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $coordinators = $this->getCoordinatorUsers();

            Notification::make()
                ->title($this->user->full_name . ' eğtimi tamamladı!')
                ->icon('heroicon-o-user')
                ->sendToDatabase($coordinators);
        } catch (\Exception $exception) {
            //
        }
    }

    /**
     * Get coordinator users.
     */
    private function getCoordinatorUsers()
    {
        return User::whereHas('roles', fn($query) => $query->where('name', 'coordinator'))->get();
    }
}

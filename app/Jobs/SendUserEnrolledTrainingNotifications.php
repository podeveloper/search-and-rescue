<?php

namespace App\Jobs;

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

class SendUserEnrolledTrainingNotifications
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $coordinators = $this->getCoordinatorUsers();

            Notification::make()
                ->title($this->user->full_name . ' eğitim içeriğine kaydoldu!')
                ->icon('heroicon-o-user')
                ->sendToDatabase($coordinators);

            Notification::make()
                ->title('Eğitim içeriğine başarıyla kaydoldunuz!')
                ->icon('heroicon-o-user')
                ->sendToDatabase([$this->user]);

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

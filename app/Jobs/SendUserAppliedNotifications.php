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

class SendUserAppliedNotifications
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
                ->title('Yeni Aday Üye Başvurusu: ' . $this->user->full_name)
                ->icon('heroicon-o-user')
                ->sendToDatabase($coordinators);

            Notification::make()
                ->title('Hoşgeldiniz: ' . $this->user->full_name)
                ->body('Başvurunuz incelendikten sonra bilgilendirileceksiniz.')
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

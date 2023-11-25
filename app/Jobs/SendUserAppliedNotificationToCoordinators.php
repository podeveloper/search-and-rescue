<?php

namespace App\Jobs;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendUserAppliedNotificationToCoordinators
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
            $coordinators = User::whereHas('roles', fn($query) => $query->where('name', 'coordinator'))->get();

            Notification::make()
                ->title('New Candidate Application: ' . $this->user->full_name)
                ->icon('heroicon-o-user')
                ->sendToDatabase($coordinators);
        }catch (\Exception $exception)
        {
            Log::info($exception->getMessage());
        }

    }
}

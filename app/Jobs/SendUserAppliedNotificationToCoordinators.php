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

    public $volunteer;

    /**
     * Create a new job instance.
     */
    public function __construct(User $volunteer)
    {
        $this->volunteer = $volunteer;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $coordinators = User::whereHas('roles', fn($query) => $query->where('name', 'coordinator'))->get();

            Notification::make()
                ->title('New Volunteer Application: ' . $this->volunteer->full_name)
                ->icon('heroicon-o-user')
                ->sendToDatabase($coordinators);
        }catch (\Exception $exception)
        {
            Log::info($exception->getMessage());
        }

    }
}

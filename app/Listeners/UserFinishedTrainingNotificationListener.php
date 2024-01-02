<?php

namespace App\Listeners;

use App\Events\UserFinishedTraining;
use App\Jobs\SendUserFinishedTrainingNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserFinishedTrainingNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserFinishedTraining $event): void
    {
        SendUserFinishedTrainingNotifications::dispatch($event->user, $event->training);
    }
}

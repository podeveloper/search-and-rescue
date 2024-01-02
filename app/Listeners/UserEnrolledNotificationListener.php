<?php

namespace App\Listeners;

use App\Events\UserEnrolled;
use App\Jobs\SendUserEnrolledTrainingNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserEnrolledNotificationListener
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
    public function handle(UserEnrolled $event): void
    {
        SendUserEnrolledTrainingNotifications::dispatch($event->user);
    }
}

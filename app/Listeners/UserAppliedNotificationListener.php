<?php

namespace App\Listeners;

use App\Events\UserApplied;
use App\Jobs\SendUserAppliedNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserAppliedNotificationListener
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
    public function handle(UserApplied $event): void
    {
        SendUserAppliedNotifications::dispatch($event->user);

    }
}

<?php

namespace App\Listeners;

use App\Events\ApplicationApproved;
use App\Jobs\SendApplicationApprovedNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ApplicationApprovedNotificationListener
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
    public function handle(ApplicationApproved $event): void
    {
        SendApplicationApprovedNotifications::dispatch($event->user);
    }
}

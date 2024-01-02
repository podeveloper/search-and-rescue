<?php

namespace App\Listeners;

use App\Events\ApplicationRejected;
use App\Jobs\EmailJob;
use App\Mail\ApplicationRejectedEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendApplicationRejectedEmailToVolunteer
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ApplicationRejected  $event
     * @return void
     */
    public function handle(ApplicationRejected $event)
    {
        EmailJob::dispatch($event->user,new ApplicationRejectedEmail($event->user));
    }
}

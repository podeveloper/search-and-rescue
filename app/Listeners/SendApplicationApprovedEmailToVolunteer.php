<?php

namespace App\Listeners;

use App\Events\ApplicationApproved;
use App\Jobs\EmailJob;
use App\Mail\ApplicationApprovedEmail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class SendApplicationApprovedEmailToVolunteer
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
     * @param  object  $event
     * @return void
     */
    public function handle(ApplicationApproved $event)
    {
        try {
            EmailJob::dispatch($event->user,new ApplicationApprovedEmail($event->user));
        }catch (\Exception $e)
        {
            //
        }
    }
}

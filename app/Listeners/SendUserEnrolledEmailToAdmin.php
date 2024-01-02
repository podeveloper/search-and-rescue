<?php

namespace App\Listeners;

use App\Events\UserEnrolled;
use App\Jobs\EmailJobByEmailString;
use App\Mail\UserEnrolledAdminEmail;
use App\Models\Training;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendUserEnrolledEmailToAdmin
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
     * @param  \App\Events\UserEnrolled  $event
     * @return void
     */
    public function handle(UserEnrolled $event)
    {
        $adminEmail = config('seed.admin.email');
        $coordinatorEmails = [$adminEmail];

        foreach ($coordinatorEmails as $email) {
            try {
                EmailJobByEmailString::dispatch($email, new UserEnrolledAdminEmail($event->user,$event->training));
            } catch (\Exception $e) {
                //
            }
        }
    }
}

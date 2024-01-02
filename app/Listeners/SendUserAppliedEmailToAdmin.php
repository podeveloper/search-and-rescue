<?php

namespace App\Listeners;

use App\Events\UserApplied;
use App\Jobs\EmailJobByEmailString;
use App\Mail\UserAppliedAdminEmail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Exception;

class SendUserAppliedEmailToAdmin
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
     * @param  \App\Events\UserApplied  $event
     * @return void
     */
    public function handle(UserApplied $event)
    {
        $adminEmail = config('seed.admin.email');
        $coordinatorEmails = [$adminEmail];

        foreach ($coordinatorEmails as $email) {
            try {
                EmailJobByEmailString::dispatch($email, new UserAppliedAdminEmail($event->user));
            } catch (Exception $e) {
                //
            }
        }
    }
}

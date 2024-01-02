<?php

namespace App\Listeners;

use App\Events\UserFinishedFirstThreeModule;
use App\Jobs\EmailJob;
use App\Jobs\EmailJobByEmailString;
use App\Jobs\SendUserFinishedFirstThreeModuleNotifications;
use App\Mail\UserFinishedFirstThreeModuleAdminEmail;
use App\Mail\UserFinishedFirstThreeModuleEmail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserFinishedFirstThreeModuleNotificationListener
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
    public function handle(UserFinishedFirstThreeModule $event): void
    {
        SendUserFinishedFirstThreeModuleNotifications::dispatch($event->user);
        EmailJob::dispatch($event->user,new UserFinishedFirstThreeModuleEmail($event->user));

        $adminEmail = config('seed.admin.email');
        $coordinatorEmails = [$adminEmail];

        foreach ($coordinatorEmails as $email) {
            try {
                EmailJobByEmailString::dispatch($email,new UserFinishedFirstThreeModuleAdminEmail($event->user));
            } catch (\Exception $e) {
                //
            }
        }
    }
}

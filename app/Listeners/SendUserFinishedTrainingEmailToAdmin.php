<?php

namespace App\Listeners;

use App\Events\UserFinishedTraining;
use App\Jobs\EmailJob;
use App\Jobs\EmailJobByEmailString;
use App\Mail\UserFinishedTrainingAdminEmail;
use App\Models\Training;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendUserFinishedTrainingEmailToAdmin
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
     * @param  \App\Events\UserFinishedTraining  $event
     * @return void
     */
    public function handle(UserFinishedTraining $event)
    {
        $training = Training::find($event->training->id);
        $user = User::find($event->user->id);

        $adminEmail = config('seed.admin.email');
        $coordinatorEmails = [$adminEmail];

        foreach ($coordinatorEmails as $email) {
            try {
                EmailJobByEmailString::dispatch($email, new UserFinishedTrainingAdminEmail($user,$training));
            } catch (\Exception $e) {
                //
            }
        }
    }
}

<?php

namespace App\Observers;

use App\Events\UserApplied;
use App\Helpers\FilamentRequest;
use App\Models\Address;
use App\Models\DataProcessingConsent;
use App\Models\RegistrationQuestionAnswer;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        $randomString = Str::random(8);
        $user->password_temp = $randomString;
        $user->password = Hash::make($randomString);

        $user->name = strtoupper($user->name);
        $user->surname = strtoupper($user->surname);
        $user->full_name = $user->name . ' ' . $user->surname;
    }

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        if ($user->isDirty('name') || $user->isDirty('surname'))  {
            $user->name = strtoupper($user->name);
            $user->surname = strtoupper($user->surname);
            $user->full_name = $user->name . ' ' . $user->surname;
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $request = FilamentRequest::get(request());

        if ($request)
        {
            if (property_exists($request,'speaking_languages'))
            {
                foreach ($request->speaking_languages as $language)
                {
                    $user->languages()->attach($language);
                }
            }

            $socialPlatforms = ['instagram','facebook','twitter'];
            foreach ($socialPlatforms as $platform)
            {
                if (property_exists($request,$platform)){
                    SocialAccount::create([
                        'user_id' => $user->id,
                        'platform' => $platform,
                        'username' => $request->$platform,
                    ]);
                }
            }

            if (property_exists($request,'country_id')) {
                Address::create([
                    'type' => 'home',
                    'country_id' => $request->country_id,
                    'city_id' => $request->city_id,
                    'district_id' => $request->district_id,
                    'user_id' => $user->id,
                ]);
            }

            $questions = $this->extract($request,'question_');
            foreach ($questions as $questionId => $answer)
            {
                RegistrationQuestionAnswer::create([
                    'text' => $answer,
                    'user_id' => $user->id,
                    'question_id' => $questionId,
                ]);
            }
        }

        DataProcessingConsent::firstOrCreate([
            'kvkk_approval' => now(),
            'explicit_consent_approval' => now(),
            'user_id' => $user->id,
        ]);

        // UserApplied::dispatch($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }

    function extract($object,$searchKey) {
        $questions = [];

        foreach ((array) $object as $key => $value) {
            if (strpos($key, $searchKey) === 0 && $value !== null) {
                $questionNumber = substr($key, strlen($searchKey));
                $questions[$questionNumber] = $value;
            }
        }

        return $questions;
    }
}

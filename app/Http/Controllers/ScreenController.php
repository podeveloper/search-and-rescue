<?php

namespace App\Http\Controllers;

use App\Helpers\HadeethOfTheDayHelper;
use App\Helpers\PrayerTimesHelper;
use App\Helpers\VerseOfTheDayHelper;
use App\Models\Event;
use App\Models\Volunteering;
use App\Models\VolunteeringPlan;

class ScreenController extends Controller
{
    public function events()
    {
        return view('livewire.incoming-events');

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Event;

class ScreenController extends Controller
{
    public function events()
    {
        return view('livewire.incoming-events');

    }
}

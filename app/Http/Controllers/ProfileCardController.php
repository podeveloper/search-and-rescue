<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileCardController extends Controller
{
    public function show($username)
    {
        $volunteer = User::where('username', $username)->first();

        if (!$volunteer) {
            abort(404);
        }

        return view('volunteers.profile-card', compact('volunteer'));
    }
}

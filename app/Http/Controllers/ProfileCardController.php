<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileCardController extends Controller
{
    public function show($username)
    {
        $member = User::where('username', $username)->first();

        if (!$member) {
            abort(404);
        }

        return view('members.profile-card', compact('member'));
    }
}

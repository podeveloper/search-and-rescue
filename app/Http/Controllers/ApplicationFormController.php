<?php

namespace App\Http\Controllers;

use Filament\Facades\Filament;
use Illuminate\Http\Request;

class ApplicationFormController extends Controller
{
    public function index()
    {
        return view('application-form-success');
    }
}

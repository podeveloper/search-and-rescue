<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function show($path)
    {
        if (Auth::check()) {
            $filePath = storage_path("app/{$path}");

            if (file_exists($filePath)) {
                return response()->file($filePath);
            }
        }

        abort(404);
    }
}

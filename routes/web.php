<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (!auth()->check())
    {
        return redirect()->to(request()->url().'/candidate/login');
    }
    return view('welcome');
});

Route::get('/files/{path}', [FileController::class, 'show'])
    ->where('path', '.*')
    ->middleware('auth')
    ->name('files.show');

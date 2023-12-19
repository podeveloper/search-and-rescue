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

Route::get('login', fn () => redirect()->route('filament.candidate.auth.login'))->name('login');

Route::get('/', function () {
    $redirectUrl = request()->url().'/candidate';
    return auth()->check() ? redirect()->to($redirectUrl) : redirect()->to($redirectUrl.'/login');
});

Route::get('/files/{path}', [FileController::class, 'show'])
    ->where('path', '.*')
    ->middleware('auth')
    ->name('files.show');

<?php

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\VolunteerTrainingController;
use App\Http\Controllers\ScreenController;
use App\Http\Controllers\ProfileCardController;
use App\Http\Controllers\ShahadahCertificateController;
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
Route::get('application-form/success',[\App\Http\Controllers\ApplicationFormController::class,'index'])->name('application-form.success');
Route::get('apply', fn () => redirect()->route('filament.candidate.auth.register'))->name('apply');
Route::get('login', fn () => redirect()->route('filament.candidate.auth.login'))->name('login');
Route::get('/download-vcard/{id}', [\App\Http\Controllers\VcardController::class,'downloadVCard'])->name('download.vcard');

Route::get('/', function () {
    $redirectUrl = request()->url().'/candidate';
    return auth()->check() ? redirect()->to($redirectUrl) : redirect()->to($redirectUrl.'/login');
});

Route::get('/incoming-events/{date?}', [ScreenController::class,'events'])->name('incoming-events.show');
Route::get('/members/@{username}', [ProfileCardController::class,'show'])->name('members.profile-card');


Route::get('/files/{folder}/{filename}', [FileController::class, 'show'])
    ->name('files.show');

Route::get('/trainings/{training}/enroll',[VolunteerTrainingController::class,'enroll'])->name('trainings.enroll');
Route::post('/trainings/sections/{section}/storeTimeData', [VolunteerTrainingController::class, 'storeTimerData'])->name('sections.storeTimeData');
Route::get('/trainings/sections/{section}',[VolunteerTrainingController::class,'show'])->name('sections.show');
Route::post('/trainings/sections/{section}/previous',[VolunteerTrainingController::class,'previous'])->name('sections.previous');
Route::post('/trainings/sections/{section}/next',[VolunteerTrainingController::class,'next'])->name('sections.next');
Route::post('/trainings/sections/{section}/complete',[VolunteerTrainingController::class,'complete'])->name('sections.complete');

Route::get('re-optimize', [\App\Http\Controllers\OptimizeController::class,'optimize'])->name('optimize.app');

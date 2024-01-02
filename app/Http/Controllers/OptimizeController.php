<?php

namespace App\Http\Controllers;

use App\Helpers\PhoneHelper;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class OptimizeController extends Controller
{
    public function optimize()
    {
        if (auth()->user()->is_admin)
        {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            \Illuminate\Support\Facades\Artisan::call('optimize');

            self::runFixes();

            Notification::make()
                ->success()
                ->title('Application re-optimized')
                ->body('Application successfully re-optimized.')
                ->send();
        }
        return redirect()->back();
    }

    public static function runFixes()
    {
        PhoneHelper::fixTurkishNumbers();
    }
}

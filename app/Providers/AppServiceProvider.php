<?php

namespace App\Providers;

use BezhanSalleh\PanelSwitch\PanelSwitch;
use Illuminate\Support\ServiceProvider;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Validation\ValidationException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                //->modalHeading('Available Panels')
                //->slideOver()
                //->simple()
                ->modalWidth('sm')
                //->icons([
                //    'admin' => 'heroicon-o-square-2-stack',
                //], $asImage = false)
                ->iconSize(16)
                ->labels([
                    'reference' => __('panel.reference'),
                    'candidate' => __('panel.candidate'),
                    'official' => __('panel.official'),
                    'network' => __('panel.network'),
                    'stock' => __('panel.stock'),
                    'coordinator' => __('panel.coordinator'),
                ])
                ->visible(true)
                //->excludes(['saas'])
                ->canSwitchPanels(true);
        });

        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };
    }
}

<?php

namespace App\Providers;

use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
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
        $this->app->singleton(
            RegistrationResponse::class,
            \App\Http\Responses\RegistrationResponse::class
        );
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
                    'reference' => __('general.panel_reference'),
                    'candidate' => __('general.panel_candidate'),
                    'official' => __('general.panel_official'),
                    'network' => __('general.panel_network'),
                    'stock' => __('general.panel_stock'),
                    'call_center' => __('general.panel_call_center'),
                    'coordinator' => __('general.panel_coordinator'),
                    'training' => __('general.panel_training'),
                    'report' => __('general.panel_report'),
                ])
                ->visible(auth()->user() && auth()->user()?->hasRole([
                        'board member',
                        'unit manager',
                        'coordinator',
                        'network operator',
                        'stock operator',
                        'reference operator',
                    ]))
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

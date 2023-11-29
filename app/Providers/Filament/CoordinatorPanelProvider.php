<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentLanguageSwitch\FilamentLanguageSwitchPlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use lockscreen\FilamentLockscreen\Http\Middleware\Locker;
use lockscreen\FilamentLockscreen\Lockscreen;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class CoordinatorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('coordinator')
            ->path('coordinator')
            ->login()
            ->passwordReset()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName(env('APP_NAME'))
            ->brandLogo(asset(str_contains(request()->url(),'login') ? 'img/login-logo.jpg' : 'img/panel-logo.jpg'))
            ->brandLogoHeight(str_contains(request()->url(),'login') ? '150px' : '50px')
            ->favicon(asset('img/favicon-32x32.png'))
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Coordinator/Resources'), for: 'App\\Filament\\Coordinator\\Resources')
            ->discoverPages(in: app_path('Filament/Coordinator/Pages'), for: 'App\\Filament\\Coordinator\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable()
                    ->timezone(config('app.timezone')),
                FilamentLanguageSwitchPlugin::make(),
                SpotlightPlugin::make(),
                ThemesPlugin::make(),
                new Lockscreen(),
            ])
            ->discoverWidgets(in: app_path('Filament/Coordinator/Widgets'), for: 'App\\Filament\\Coordinator\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetTheme::class
            ])
            ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
                Locker::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Re-optimize')
                    ->url('/re-optimize')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (): bool => auth()->user()?->is_admin ? true : false)
            ])
            ->spa();
    }
}

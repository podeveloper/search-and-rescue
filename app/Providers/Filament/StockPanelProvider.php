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

class StockPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('stock')
            ->path('stock')
            ->login()
            ->passwordReset()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources([
                //
            ])
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Stock/Resources'), for: 'App\\Filament\\Stock\\Resources')
            ->discoverPages(in: app_path('Filament/Stock/Pages'), for: 'App\\Filament\\Stock\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentLanguageSwitchPlugin::make(),
                SpotlightPlugin::make(),
                ThemesPlugin::make(),
                new Lockscreen(),
            ])
            ->discoverWidgets(in: app_path('Filament/Stock/Widgets'), for: 'App\\Filament\\Stock\\Widgets')
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
<?php

namespace App\Providers\Filament;

use App\Filament\Candidate\Pages\Auth\Apply;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CandidatePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('candidate')
            ->path('candidate')
            ->login()
            ->registration(Apply::class)
            ->passwordReset()
            ->profile()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName(env('APP_NAME'))
            ->brandLogo(asset(str_contains(request()->url(),'login') ? 'img/login-logo.jpg' : 'img/panel-logo.jpg'))
            ->brandLogoHeight(str_contains(request()->url(),'login') ? '150px' : '50px')
            ->maxContentWidth('full')
            ->favicon(asset('img/favicon-32x32.png'))
            ->discoverResources(in: app_path('Filament/Candidate/Resources'), for: 'App\\Filament\\Candidate\\Resources')
            ->discoverPages(in: app_path('Filament/Candidate/Pages'), for: 'App\\Filament\\Candidate\\Pages')
            ->pages([
                //
            ])
            //->discoverWidgets(in: app_path('Filament/Candidate/Widgets'), for: 'App\\Filament\\Candidate\\Widgets')
            ->widgets([
                //
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
            ])
            ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationItems([
                NavigationItem::make('Incoming Events')
                    ->icon('heroicon-o-star')
                    ->group('Events')
                    ->sort(96)
                    ->url(url('/incoming-events'),true),
                NavigationItem::make('Help & Whatsapp Support')
                    ->icon('heroicon-o-question-mark-circle')
                    ->group('Help')
                    ->sort(99)
                    ->url('https://wa.me/'.config('foundation.support_phone'),true),
            ])
            ->spa();
    }
}

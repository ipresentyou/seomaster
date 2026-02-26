<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('SEOmaster')
            ->colors([
                'primary'   => Color::Blue,
                'gray'      => Color::Slate,
                'danger'    => Color::Red,
                'info'      => Color::Blue,
                'success'   => Color::Green,
                'warning'   => Color::Orange,
            ])
            ->font('Roboto', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap')
            ->login()
            ->registration()
            ->emailVerification()
            ->passwordReset()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->renderHook('panels::head.end', fn () => '
                <style>
                    header.fi-topbar, .fi-topbar {
                        background-color: #fff !important;
                        border-bottom: 1px solid #dadce0 !important;
                        box-shadow: none !important;
                    }
                    aside.fi-sidebar, .fi-sidebar, .fi-sidebar-nav {
                        background-color: #fff !important;
                        border-right: 1px solid #dadce0 !important;
                    }
                    .fi-sidebar-header {
                        background-color: #fff !important;
                        border-bottom: 1px solid #dadce0 !important;
                    }
                    .fi-logo-text, .fi-brand-name,
                    .fi-sidebar-header a, .fi-sidebar-header span {
                        color: #202124 !important;
                    }
                    .fi-sidebar-nav-item-label,
                    .fi-sidebar-nav-item span,
                    .fi-sidebar-nav-item a {
                        color: #3c4043 !important;
                    }
                    .fi-sidebar-nav-item.fi-active .fi-sidebar-nav-item-label {
                        color: #1a73e8 !important;
                        font-weight: 500 !important;
                    }
                    .fi-sidebar-nav-item.fi-active {
                        background-color: #e8f0fe !important;
                    }
                    .fi-sidebar-nav-item:hover {
                        background-color: #f1f3f4 !important;
                    }
                    .fi-sidebar-nav-item svg { color: #5f6368 !important; }
                    .fi-sidebar-nav-item.fi-active svg { color: #1a73e8 !important; }
                    .fi-sidebar-group-label {
                        color: #5f6368 !important;
                        font-size: 11px !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.08em !important;
                        font-weight: 500 !important;
                    }
                    main.fi-main, .fi-main, .fi-body, body {
                        background-color: #f8f9fa !important;
                    }
                    .fi-header-heading, h1.fi-header-heading {
                        color: #202124 !important;
                        font-weight: 400 !important;
                    }
                    .fi-section, .fi-card, .fi-wi {
                        background-color: #fff !important;
                        border: 1px solid #dadce0 !important;
                        box-shadow: none !important;
                        border-radius: 8px !important;
                    }
                    .fi-section-header-heading,
                    .fi-card-header-heading {
                        color: #202124 !important;
                        font-weight: 500 !important;
                    }
                    p, span, label, td, th,
                    .fi-fo-field-wrp-label,
                    .fi-ta-cell { color: #202124 !important; }
                    .fi-fo-field-wrp-helper-text { color: #5f6368 !important; }
                    input, select, textarea, .fi-input {
                        background-color: #fff !important;
                        border: 1px solid #dadce0 !important;
                        color: #202124 !important;
                        border-radius: 4px !important;
                    }
                    input:focus, select:focus, textarea:focus {
                        border-color: #1a73e8 !important;
                        box-shadow: 0 0 0 2px rgba(26,115,232,0.2) !important;
                    }
                    .fi-ta-header-cell, th {
                        background-color: #f8f9fa !important;
                        color: #5f6368 !important;
                        font-size: 12px !important;
                        font-weight: 500 !important;
                        text-transform: uppercase !important;
                    }
                    .fi-wi-stats-overview-stat {
                        background: #fff !important;
                        border: 1px solid #dadce0 !important;
                        border-radius: 8px !important;
                        box-shadow: none !important;
                    }
                    .fi-wi-stats-overview-stat-value {
                        color: #202124 !important;
                        font-weight: 500 !important;
                    }
                    .fi-wi-stats-overview-stat-label,
                    .fi-wi-stats-overview-stat-description {
                        color: #5f6368 !important;
                    }
                    .fi-btn-primary {
                        background-color: #1a73e8 !important;
                        color: #fff !important;
                        border-radius: 4px !important;
                        box-shadow: none !important;
                    }
                    .fi-dropdown-panel {
                        background: #fff !important;
                        border: 1px solid #dadce0 !important;
                        border-radius: 8px !important;
                    }
                    .fi-dropdown-list-item-label { color: #202124 !important; }
                </style>
            ')
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

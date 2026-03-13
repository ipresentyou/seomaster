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
                'primary'   => Color::hex('#1a73e8'),  // Google Blue
                'gray'      => Color::hex('#5f6368'),  // Google Gray
                'danger'    => Color::hex('#d93025'),  // Google Red
                'info'      => Color::hex('#1a73e8'),  // Google Blue
                'success'   => Color::hex('#1e8e3e'),  // Google Green
                'warning'   => Color::hex('#e37400'),  // Google Orange
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
                    /* Complete Google Material Design Theme */
                    :root {
                        --primary: 26, 115, 232;
                        --primary-rgb: 26, 115, 232;
                        --gray: 95, 99, 104;
                        --danger: 217, 48, 37;
                        --success: 30, 142, 62;
                        --warning: 227, 116, 0;
                        --info: 26, 115, 232;
                    }
                    
                    /* Override all Filament colors */
                    .fi-btn-primary { 
                        background: rgb(var(--primary)) !important; 
                        border-color: rgb(var(--primary)) !important;
                        color: #fff !important;
                    }
                    .fi-btn-primary:hover { 
                        background: rgb(var(--primary-rgb) / 0.9) !important; 
                        border-color: rgb(var(--primary-rgb) / 0.9) !important;
                    }
                    
                    /* Dark mode overrides */
                    .dark .fi-btn-primary {
                        background: rgb(var(--primary)) !important;
                        border-color: rgb(var(--primary)) !important;
                    }
                    
                    /* Remove all shadows and borders */
                    .fi-section, .fi-card, .fi-wi {
                        background: #fff !important;
                        border: 1px solid #dadce0 !important;
                        box-shadow: none !important;
                        border-radius: 8px !important;
                    }
                    
                    .dark .fi-section, .dark .fi-card, .dark .fi-wi {
                        background: #202124 !important;
                        border: 1px solid #3c4043 !important;
                    }
                    
                    /* Widget specific styling */
                    .fi-wi-stats-overview-stat {
                        background: #fff !important;
                        border: 1px solid #dadce0 !important;
                        border-radius: 8px !important;
                        box-shadow: none !important;
                        padding: 20px !important;
                    }
                    
                    .dark .fi-wi-stats-overview-stat {
                        background: #202124 !important;
                        border: 1px solid #3c4043 !important;
                    }
                    
                    .fi-wi-stats-overview-stat-value {
                        color: #202124 !important;
                        font-weight: 500 !important;
                        font-size: 24px !important;
                    }
                    
                    .dark .fi-wi-stats-overview-stat-value {
                        color: #fff !important;
                    }
                    
                    .fi-wi-stats-overview-stat-label,
                    .fi-wi-stats-overview-stat-description {
                        color: #5f6368 !important;
                        font-size: 14px !important;
                    }
                    
                    .dark .fi-wi-stats-overview-stat-label,
                    .dark .fi-wi-stats-overview-stat-description {
                        color: #9aa0a6 !important;
                    }
                    
                    /* Typography */
                    .fi-header-heading, h1.fi-header-heading {
                        color: #202124 !important;
                        font-weight: 400 !important;
                    }
                    
                    .dark .fi-header-heading, .dark h1.fi-header-heading {
                        color: #fff !important;
                    }
                    
                    /* Sidebar */
                    .fi-sidebar {
                        background: #fff !important;
                        border-right: 1px solid #dadce0 !important;
                    }
                    
                    .dark .fi-sidebar {
                        background: #202124 !important;
                        border-right: 1px solid #3c4043 !important;
                    }
                    
                    /* Topbar */
                    .fi-topbar {
                        background: #fff !important;
                        border-bottom: 1px solid #dadce0 !important;
                        box-shadow: none !important;
                    }
                    
                    .dark .fi-topbar {
                        background: #202124 !important;
                        border-bottom: 1px solid #3c4043 !important;
                    }
                    
                    /* Main content */
                    .fi-main, .fi-body, body {
                        background: #f8f9fa !important;
                    }
                    
                    .dark .fi-main, .dark .fi-body, .dark body {
                        background: #121212 !important;
                    }
                    
                    /* Dashboard grid spacing */
                    .fi-wi {
                        margin-bottom: 24px !important;
                    }
                    
                    .fi-wi-stats-overview {
                        gap: 24px !important;
                    }
                    
                    /* Fix widget grid gaps */
                    .fi-wi-grid {
                        gap: 24px !important;
                    }
                    
                    .grid.gap-6 {
                        gap: 24px !important;
                    }
                    
                    .grid.md\\:grid-cols-3.gap-6 {
                        gap: 24px !important;
                    }
                    
                    /* Stats overview specific */
                    .fi-wi-stats-overview-stats-ctn {
                        gap: 24px !important;
                    }
                    
                    .fi-wi-stats-overview-stats-ctn.md\:grid-cols-3 {
                        gap: 24px !important;
                    }
                    
                    /* Individual stat widgets spacing */
                    .fi-wi-stats-overview-stat {
                        margin-bottom: 0 !important;
                    }
                    
                    .fi-wi-stats-overview .fi-wi-stats-overview-stat {
                        margin: 0 !important;
                        padding: 20px !important;
                    }
                    
                    /* Ensure proper spacing in grid */
                    .fi-wi-stats-overview-stats-ctn .fi-wi-stats-overview-stat {
                        margin: 0 !important;
                    }
                    
                    /* Grid layout improvements */
                    .fi-wi-grid {
                        gap: 24px !important;
                    }
                    
                    .fi-wi > div {
                        margin-bottom: 0 !important;
                    }
                    
                    /* Widget container spacing */
                    .fi-section {
                        margin-bottom: 24px !important;
                    }
                    
                    /* Stats overview specific */
                    .fi-wi-stats-overview .fi-wi-stats-overview-stat {
                        margin: 0 !important;
                    }
                    
                    /* Chart widgets */
                    .fi-wi-chart {
                        padding: 20px !important;
                    }
                    
                    /* Activity feed */
                    .fi-wi-activity-feed {
                        padding: 20px !important;
                    }
                    
                    /* All pages - consistent spacing */
                    .fi-page {
                        padding: 24px !important;
                    }
                    
                    .fi-page-content {
                        gap: 24px !important;
                    }
                    
                    /* Resource pages */
                    .fi-resource-page {
                        padding: 24px !important;
                    }
                    
                    /* Tables */
                    .fi-table-container {
                        margin-bottom: 24px !important;
                    }
                    
                    /* Forms */
                    .fi-form-container {
                        padding: 24px !important;
                    }
                    
                    /* Cards and sections everywhere */
                    .fi-card, .fi-section {
                        margin-bottom: 24px !important;
                    }
                    
                    /* List pages */
                    .fi-list-page {
                        padding: 24px !important;
                    }
                    
                    /* Edit/Create pages */
                    .fi-record-page {
                        padding: 24px !important;
                    }
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

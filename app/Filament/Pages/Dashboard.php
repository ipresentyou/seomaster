<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ActivityFeedWidget;
use App\Filament\Widgets\AdminStatsWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Filament\Widgets\UserGrowthWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $title = 'Dashboard';
    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [
            AdminStatsWidget::class,
            RevenueChartWidget::class,
            UserGrowthWidget::class,
            ActivityFeedWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_platform')
                ->label('Zur Plattform')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url('/')
                ->openUrlInNewTab()
                ->color('gray'),
        ];
    }
}

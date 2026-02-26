<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Umsatz (12 Monate)';
    protected static ?int $sort = 2;
    protected static string $color = 'success';

    protected function getData(): array
    {
        $data = collect(range(11, 0))->map(function (int $monthsAgo) {
            $month = now()->subMonths($monthsAgo);
            return [
                'month'   => $month->format('M y'),
                'revenue' => 0,
            ];
        });

        return [
            'datasets' => [[
                'label' => 'Umsatz (€)',
                'data'  => $data->pluck('revenue')->toArray(),
                'borderColor' => '#10b981',
                'backgroundColor' => 'rgba(16,185,129,0.1)',
                'fill' => true,
                'tension' => 0.4,
            ]],
            'labels' => $data->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

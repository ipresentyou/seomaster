<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserGrowthWidget extends ChartWidget
{
    protected static ?string $heading = 'Neue Registrierungen (30 Tage)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;
    protected static ?string $maxHeight = '220px';

    protected function getData(): array
    {
        $data = collect(range(29, 0))->map(function (int $daysAgo) {
            $day   = now()->subDays($daysAgo);
            $count = User::whereDate('created_at', $day)->count();
            return ['day' => $day->format('d.m'), 'count' => $count];
        });

        return [
            'datasets' => [
                [
                    'label'           => 'Neue User',
                    'data'            => $data->pluck('count')->toArray(),
                    'backgroundColor' => 'rgba(124, 58, 237, 0.6)',
                    'borderColor'     => '#7c3aed',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $data->pluck('day')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales'  => [
                'x' => [
                    'grid'  => ['display' => false],
                    'ticks' => ['color' => '#5b5880', 'maxTicksLimit' => 8],
                ],
                'y' => [
                    'grid'        => ['color' => 'rgba(255,255,255,0.05)'],
                    'ticks'       => ['color' => '#5b5880'],
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}

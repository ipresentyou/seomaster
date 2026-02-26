<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected ?string $heading = 'Übersicht';

    protected function getStats(): array
    {
        $totalUsers   = User::count();
        $activeUsers  = User::where('status', 'active')->count();
        $newThisWeek  = User::where('created_at', '>=', now()->subWeek())->count();
        $activeSubs   = Subscription::where('status', 'active')->count();
        $trialSubs    = Subscription::where('status', 'trial')->where('trial_ends_at', '>', now())->count();
        $cancelledSubs = Subscription::where('status', 'cancelled')->where('cancelled_at', '>=', now()->subMonth())->count();

        $mrr = Subscription::where('status', 'active')
            ->with('plan')->get()
            ->sum(fn($s) => $s->plan?->price_monthly ?? 0);

        return [
            Stat::make('Gesamt User', number_format($totalUsers))
                ->description("+{$newThisWeek} diese Woche · {$activeUsers} aktiv")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Aktive Abos', number_format($activeSubs))
                ->description("{$trialSubs} Trial · {$cancelledSubs} gekündigt")
                ->descriptionIcon('heroicon-m-credit-card')
                ->icon('heroicon-o-credit-card')
                ->color('success'),

            Stat::make('MRR (geschätzt)', '€ ' . number_format($mrr, 2))
                ->description('Monatlich wiederkehrender Umsatz')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->icon('heroicon-o-banknotes')
                ->color('warning'),
        ];
    }
}

<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use App\Models\Subscription;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Neues Abo'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Alle')
                ->badge(Subscription::count()),
            'active' => Tab::make('Aktiv')
                ->badge(Subscription::where('status', 'active')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('status', 'active')),
            'trial' => Tab::make('Trial')
                ->badge(Subscription::where('status', 'trial')->where('trial_ends_at', '>', now())->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('status', 'trial')),
            'cancelled' => Tab::make('Gekündigt')
                ->badge(Subscription::where('status', 'cancelled')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('status', 'cancelled')),
        ];
    }
}

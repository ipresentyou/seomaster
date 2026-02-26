<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Neuer User')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Alle')
                ->badge(User::count()),

            'active' => Tab::make('Aktiv')
                ->badge(User::where('status', 'active')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('status', 'active')),

            'pending' => Tab::make('Ausstehend')
                ->badge(User::where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('status', 'pending')),

            'suspended' => Tab::make('Gesperrt')
                ->badge(User::where('status', 'suspended')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('status', 'suspended')),
        ];
    }
}

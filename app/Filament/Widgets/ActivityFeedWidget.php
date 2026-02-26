<?php

namespace App\Filament\Widgets;

use App\Models\SeoActivityLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActivityFeedWidget extends BaseWidget
{
    protected static ?string $heading = '⚡ Live Activity Feed';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static ?int $defaultPaginationPageOption = 10;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SeoActivityLog::query()
                    ->with(['user', 'project'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Zeit')
                    ->dateTime('d.m.y H:i')
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\BadgeColumn::make('action')
                    ->label('Aktion')
                    ->colors([
                        'success' => fn($state) => str_contains($state, 'saved'),
                        'primary' => fn($state) => str_contains($state, 'generated'),
                        'warning' => fn($state) => str_contains($state, 'deleted'),
                    ])
                    ->formatStateUsing(fn(string $state) => match(true) {
                        str_contains($state, 'alt_text.generated')   => '🖼️ Alt-Text generiert',
                        str_contains($state, 'alt_text.saved')       => '💾 Alt-Text gespeichert',
                        str_contains($state, 'meta.generated')       => '✨ Meta generiert',
                        str_contains($state, 'meta.saved')           => '💾 Meta gespeichert',
                        str_contains($state, 'seotext.generated')    => '📝 SEO-Text generiert',
                        str_contains($state, 'seotext.saved')        => '💾 SEO-Text gespeichert',
                        default                                       => $state,
                    }),

                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Typ')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn(?string $state) => match($state) {
                        'product'  => '🛍️ Produkt',
                        'category' => '📁 Kategorie',
                        'media'    => '🖼️ Bild',
                        default    => $state ?? '—',
                    }),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Projekt')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('ai_tokens_used')
                    ->label('Tokens')
                    ->numeric()
                    ->color(fn(int $state) => $state > 1000 ? 'warning' : 'gray')
                    ->placeholder('0'),
            ])
            ->striped()
            ->paginated([10, 25, 50])
            ->poll('15s');
    }
}

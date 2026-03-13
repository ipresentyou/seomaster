<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\SeoActivityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogResource extends Resource
{
    protected static ?string $model = SeoActivityLog::class;
    protected static ?string $navigationIcon  = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'SEO Platform';
    protected static ?string $navigationLabel = 'Aktivitäts-Log';
    protected static ?int    $navigationSort  = 3;

    // Read-only: disable create button
    public static function canCreate(): bool { return false; }

    public static function getNavigationBadge(): ?string
    {
        $count = SeoActivityLog::where('created_at', '>=', now()->subDay())->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string { return 'info'; }

    // ── Form (Edit-only, mostly read-only display) ────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Details')->schema([
                Forms\Components\TextInput::make('action')
                    ->label('Aktion')
                    ->readOnly(),

                Forms\Components\TextInput::make('entity_type')
                    ->label('Entität')
                    ->readOnly(),

                Forms\Components\TextInput::make('entity_id')
                    ->label('Entitäts-ID')
                    ->readOnly(),

                Forms\Components\TextInput::make('ai_tokens_used')
                    ->label('Tokens')
                    ->readOnly(),

                Forms\Components\Textarea::make('payload')
                    ->label('Payload (JSON)')
                    ->readOnly()
                    ->columnSpanFull()
                    ->rows(6),
            ])->columns(2),
        ]);
    }

    // ── Table ─────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->poll('15s')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Zeit')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->description(fn(SeoActivityLog $l) => $l->user?->email ?? ''),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Projekt')
                    ->placeholder('—'),

                Tables\Columns\BadgeColumn::make('action')
                    ->label('Aktion')
                    ->colors([
                        'success' => fn($state) => str_contains($state, 'saved'),
                        'primary' => fn($state) => str_contains($state, 'generated'),
                        'info'    => fn($state) => str_contains($state, 'alt_text'),
                        'warning' => fn($state) => str_contains($state, 'error'),
                    ])
                    ->formatStateUsing(fn(string $state) => match(true) {
                        str_contains($state, 'alt_text.saved')      => '🖼️ Alt gespeichert',
                        str_contains($state, 'alt_text.generated')  => '🤖 Alt generiert',
                        str_contains($state, 'meta.saved')          => '💾 Meta gespeichert',
                        str_contains($state, 'meta.generated')      => '✨ Meta generiert',
                        default                                      => $state,
                    }),

                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Typ')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn($state) => match($state) {
                        'product'  => '🛍️ Produkt',
                        'category' => '📁 Kategorie',
                        'media'    => '🖼️ Media',
                        default    => $state,
                    }),

                Tables\Columns\TextColumn::make('ai_tokens_used')
                    ->label('Tokens')
                    ->numeric()
                    ->alignRight()
                    ->placeholder('0')
                    ->description('AI Token-Verbrauch'),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'email')
                    ->label('User')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('entity_type')
                    ->label('Entitätstyp')
                    ->options([
                        'product'  => '🛍️ Produkt',
                        'category' => '📁 Kategorie',
                        'media'    => '🖼️ Media',
                    ]),

                Tables\Filters\Filter::make('today')
                    ->label('Heute')
                    ->query(fn(Builder $q) => $q->whereDate('created_at', today())),

                Tables\Filters\Filter::make('this_week')
                    ->label('Diese Woche')
                    ->query(fn(Builder $q) => $q->where('created_at', '>=', now()->startOfWeek())),

                Tables\Filters\Filter::make('with_tokens')
                    ->label('Mit Token-Verbrauch')
                    ->query(fn(Builder $q) => $q->where('ai_tokens_used', '>', 0)),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view'  => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}

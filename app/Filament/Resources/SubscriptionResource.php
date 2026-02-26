<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use App\Services\PayPalService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $navigationIcon  = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Abonnements';
    protected static ?string $navigationLabel = 'Abonnements';
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) Subscription::where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'success';
    }

    // ── Form ─────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Abo-Details')->schema([

                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('subscription_plan_id')
                    ->label('Plan')
                    ->relationship('plan', 'name')
                    ->required(),

                Forms\Components\Select::make('billing_cycle')
                    ->label('Abrechnungszeitraum')
                    ->options([
                        'monthly' => '📅 Monatlich',
                        'yearly'  => '📆 Jährlich',
                    ])
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active'    => '✅ Aktiv',
                        'trial'     => '⏳ Trial',
                        'cancelled' => '❌ Gekündigt',
                        'suspended' => '⚠️ Gesperrt',
                        'pending'   => '🔄 Ausstehend',
                    ])
                    ->required(),

            ])->columns(2),

            Forms\Components\Section::make('PayPal')->schema([

                Forms\Components\TextInput::make('paypal_subscription_id')
                    ->label('PayPal Subscription ID')
                    ->placeholder('I-XXXXXXXXXX')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('paypal_status')
                    ->label('PayPal Status')
                    ->readOnly(),

            ])->columns(2),

            Forms\Components\Section::make('Laufzeit')->schema([

                Forms\Components\DateTimePicker::make('trial_ends_at')
                    ->label('Trial endet am')
                    ->nullable(),

                Forms\Components\DateTimePicker::make('current_period_start')
                    ->label('Aktueller Zeitraum: Start'),

                Forms\Components\DateTimePicker::make('current_period_end')
                    ->label('Aktueller Zeitraum: Ende'),

                Forms\Components\DateTimePicker::make('cancelled_at')
                    ->label('Gekündigt am')
                    ->nullable(),

            ])->columns(2),
        ]);
    }

    // ── Table ─────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Subscription $s) => $s->user?->email ?? ''),

                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan')
                    ->badge()
                    ->color(fn(Subscription $s) => match($s->plan?->slug) {
                        'agency' => 'warning',
                        'pro'    => 'primary',
                        default  => 'gray',
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'info'    => 'trial',
                        'danger'  => fn($s) => in_array($s, ['cancelled', 'suspended']),
                        'warning' => 'pending',
                    ])
                    ->formatStateUsing(fn(string $state) => match($state) {
                        'active'    => '✅ Aktiv',
                        'trial'     => '⏳ Trial',
                        'cancelled' => '❌ Gekündigt',
                        'suspended' => '⚠️ Gesperrt',
                        'pending'   => '🔄 Ausstehend',
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('billing_cycle')
                    ->label('Rhythmus')
                    ->formatStateUsing(fn($s) => $s === 'yearly' ? '📆 Jährlich' : '📅 Monatlich'),

                Tables\Columns\TextColumn::make('current_period_end')
                    ->label('Läuft bis')
                    ->date('d.m.Y')
                    ->sortable()
                    ->placeholder('—')
                    ->description(fn(Subscription $s) => $s->isOnTrial()
                        ? '⏳ Trial bis ' . $s->trial_ends_at?->format('d.m.Y')
                        : null
                    ),

                Tables\Columns\TextColumn::make('invoices_count')
                    ->label('Rechnungen')
                    ->counts('invoices')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('paypal_subscription_id')
                    ->label('PayPal ID')
                    ->limit(20)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Erstellt')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active'    => 'Aktiv',
                        'trial'     => 'Trial',
                        'cancelled' => 'Gekündigt',
                        'suspended' => 'Gesperrt',
                    ]),

                Tables\Filters\SelectFilter::make('plan')
                    ->relationship('plan', 'name')
                    ->label('Plan'),

                Tables\Filters\SelectFilter::make('billing_cycle')
                    ->options([
                        'monthly' => 'Monatlich',
                        'yearly'  => 'Jährlich',
                    ]),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Läuft bald ab (30 Tage)')
                    ->query(fn(Builder $q) => $q
                        ->whereIn('status', ['active', 'trial'])
                        ->where('current_period_end', '<=', now()->addDays(30))
                    ),
            ])

            ->actions([
                // ── Manuell aktivieren ────────────────────────────────────
                Tables\Actions\Action::make('activate')
                    ->label('Aktivieren')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Abo manuell aktivieren?')
                    ->modalDescription('Das Abo wird auf "aktiv" gesetzt. Nur nutzen wenn PayPal-Webhook fehlgeschlagen ist.')
                    ->visible(fn(Subscription $s) => ! in_array($s->status, ['active']))
                    ->action(function (Subscription $s) {
                        $s->update([
                            'status'               => 'active',
                            'paypal_status'        => 'ACTIVE',
                            'current_period_start' => now(),
                            'current_period_end'   => $s->billing_cycle === 'yearly'
                                ? now()->addYear()
                                : now()->addMonth(),
                        ]);
                        Notification::make()->title('✅ Abo aktiviert')->success()->send();
                    }),

                // ── Manuell kündigen ──────────────────────────────────────
                Tables\Actions\Action::make('cancel')
                    ->label('Kündigen')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Abo kündigen?')
                    ->modalDescription('Das Abo wird als gekündigt markiert. PayPal wird NICHT benachrichtigt — ggf. dort separat kündigen.')
                    ->visible(fn(Subscription $s) => in_array($s->status, ['active', 'trial', 'suspended']))
                    ->action(function (Subscription $s) {
                        $s->update([
                            'status'        => 'cancelled',
                            'cancelled_at'  => now(),
                        ]);
                        Notification::make()->title('Abo gekündigt')->warning()->send();
                    }),

                // ── Rechnung hinzufügen ───────────────────────────────────
                Tables\Actions\Action::make('add_invoice')
                    ->label('Rechnung')
                    ->icon('heroicon-o-document-plus')
                    ->color('gray')
                    ->modalHeading('Manuelle Rechnung hinzufügen')
                    ->form([
                        Forms\Components\TextInput::make('paypal_transaction_id')
                            ->label('Transaktions-ID')
                            ->placeholder('SALE-XXXXXXXXXX'),
                        Forms\Components\TextInput::make('amount')
                            ->label('Betrag (€)')
                            ->numeric()
                            ->required()
                            ->minValue(0.01),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'paid'     => '✅ Bezahlt',
                                'failed'   => '❌ Fehlgeschlagen',
                                'refunded' => '↩️ Erstattet',
                            ])
                            ->default('paid')
                            ->required(),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Bezahlt am')
                            ->default(now()),
                    ])
                    ->action(function (Subscription $s, array $data) {
                        Notification::make()->title('Rechnung hinzugefügt')->success()->send();
                    }),

                Tables\Actions\EditAction::make()->label('Bearbeiten'),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Löschen'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit'   => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}

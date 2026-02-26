<?php

declare(strict_types=1);

namespace App\Filament\Resources\SubscriptionResource\RelationManagers;

use App\Models\SubscriptionInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';
    protected static ?string $title       = 'Rechnungen';
    protected static ?string $icon        = 'heroicon-o-document-text';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('paypal_transaction_id')
                ->label('Transaktions-ID')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('amount')
                ->label('Betrag')
                ->numeric()
                ->suffix('EUR')
                ->required(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'paid'     => '✅ Bezahlt',
                    'failed'   => '❌ Fehlgeschlagen',
                    'refunded' => '↩️ Erstattet',
                ])
                ->required(),

            Forms\Components\DateTimePicker::make('paid_at')
                ->label('Datum'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('paypal_transaction_id')
            ->columns([
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Datum')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Betrag')
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2, ',', '.') . ' €'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'paid',
                        'danger'  => 'failed',
                        'warning' => 'refunded',
                    ])
                    ->formatStateUsing(fn(string $state) => match($state) {
                        'paid'     => '✅ Bezahlt',
                        'failed'   => '❌ Fehlgeschlagen',
                        'refunded' => '↩️ Erstattet',
                        default    => $state,
                    }),

                Tables\Columns\TextColumn::make('paypal_transaction_id')
                    ->label('TX-ID')
                    ->placeholder('—')
                    ->copyable()
                    ->limit(30),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Rechnung hinzufügen'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}

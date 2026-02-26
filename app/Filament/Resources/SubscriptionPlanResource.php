<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionPlanResource\Pages;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Abonnements';
    protected static ?string $navigationLabel = 'Pläne';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $featureOptions = [
            'seo_products'  => '🏷️ Produkte SEO',
            'seo_categories'=> '📁 Kategorien SEO',
            'alt_text'      => '🖼️ Alt-Text Generator',
            'gsc_dashboard' => '📊 GSC Dashboard',
            'bulk_generate' => '⚡ Bulk Generierung',
            'export_csv'    => '📤 CSV Export',
        ];

        return $form->schema([
            Forms\Components\Section::make('Plan Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('description')
                    ->rows(2),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktiv')
                    ->default(true),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ])->columns(2),

            Forms\Components\Section::make('Preise')->schema([
                Forms\Components\TextInput::make('price_monthly')
                    ->label('Preis/Monat (€)')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('price_yearly')
                    ->label('Preis/Jahr (€)')
                    ->numeric(),

                Forms\Components\TextInput::make('paypal_plan_id_monthly')
                    ->label('PayPal Plan ID (monatlich)'),

                Forms\Components\TextInput::make('paypal_plan_id_yearly')
                    ->label('PayPal Plan ID (jährlich)'),
            ])->columns(2),

            Forms\Components\Section::make('Limits & Features')->schema([
                Forms\Components\TextInput::make('max_shops')
                    ->label('Max. Shops')
                    ->numeric()
                    ->default(1),

                Forms\Components\TextInput::make('max_api_calls_per_day')
                    ->label('Max. API-Calls/Tag')
                    ->numeric()
                    ->default(100),

                Forms\Components\CheckboxList::make('features')
                    ->label('Enthaltene Features')
                    ->options($featureOptions)
                    ->columns(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable(),
                Tables\Columns\TextColumn::make('price_monthly')
                    ->label('€/Monat')
                    ->money('EUR'),
                Tables\Columns\TextColumn::make('price_yearly')
                    ->label('€/Jahr')
                    ->money('EUR'),
                Tables\Columns\TextColumn::make('max_shops')
                    ->label('Shops'),
                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->label('Abonnenten')
                    ->counts('subscriptions'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktiv')
                    ->boolean(),
            ])
            ->reorderable('sort_order')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'edit'   => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}

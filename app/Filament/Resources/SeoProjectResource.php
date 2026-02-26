<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoProjectResource\Pages;
use App\Models\ApiCredential;
use App\Models\SeoProject;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeoProjectResource extends Resource
{
    protected static ?string $model = SeoProject::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'SEO Platform';
    protected static ?string $navigationLabel = 'Projekte';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Projekt-Details')->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Inhaber')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->label('Projektname')
                    ->required()
                    ->placeholder('z.B. Shop DE'),

                Forms\Components\TextInput::make('shopware_url')
                    ->label('Shopware URL')
                    ->url()
                    ->required()
                    ->placeholder('https://shop.example.com'),

                Forms\Components\Select::make('locale')
                    ->label('Locale')
                    ->options([
                        'de-DE' => '🇩🇪 Deutsch',
                        'en-GB' => '🇬🇧 English',
                        'fr-FR' => '🇫🇷 Français',
                        'nl-NL' => '🇳🇱 Nederlands',
                    ])
                    ->default('de-DE'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktiv')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Projekt')
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('shopware_url')
                    ->label('Shop URL')
                    ->limit(40)
                    ->url(fn(SeoProject $r) => $r->shopware_url)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('locale')
                    ->label('Sprache')
                    ->badge(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktiv')
                    ->boolean(),

                Tables\Columns\TextColumn::make('activityLogs_count')
                    ->label('Aktionen')
                    ->counts('activityLogs')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Erstellt')
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('locale')
                    ->options([
                        'de-DE' => 'Deutsch',
                        'en-GB' => 'English',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktiv'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSeoProjects::route('/'),
            'create' => Pages\CreateSeoProject::route('/create'),
            'edit'   => Pages\EditSeoProject::route('/{record}/edit'),
        ];
    }
}

// ─── Pages (inline) ───────────────────────────────────────────────────────────

namespace App\Filament\Resources\SeoProjectResource\Pages;

use App\Filament\Resources\SeoProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;

class ListSeoProjects extends ListRecords
{
    protected static string $resource = SeoProjectResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Neues Projekt')];
    }
}

class CreateSeoProject extends CreateRecord
{
    protected static string $resource = SeoProjectResource::class;
}

class EditSeoProject extends EditRecord
{
    protected static string $resource = SeoProjectResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}

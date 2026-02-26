<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Benutzerverwaltung';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Profil')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('E-Mail')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('password')
                    ->label('Passwort')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context) => $context === 'create'),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active'    => '✅ Aktiv',
                        'suspended' => '🚫 Gesperrt',
                        'pending'   => '⏳ Ausstehend',
                    ])
                    ->required(),

                Forms\Components\Select::make('timezone')
                    ->label('Zeitzone')
                    ->options(
                        collect(timezone_identifiers_list())
                            ->mapWithKeys(fn($tz) => [$tz => $tz])
                            ->toArray()
                    )
                    ->searchable(),
            ])->columns(2),

            Forms\Components\Section::make('Rollen & Rechte')->schema([
                Forms\Components\CheckboxList::make('roles')
                    ->label('Rollen')
                    ->relationship('roles', 'name')
                    ->columns(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn(User $u) => "https://ui-avatars.com/api/?name={$u->name}&background=1a73e8&color=fff"),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-Mail')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger'  => 'suspended',
                        'warning' => 'pending',
                    ]),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rollen')
                    ->badge(),

                Tables\Columns\TextColumn::make('activeSubscription.plan.name')
                    ->label('Abo')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('seoProjects_count')
                    ->label('Projekte')
                    ->counts('seoProjects'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registriert')
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active'    => 'Aktiv',
                        'suspended' => 'Gesperrt',
                        'pending'   => 'Ausstehend',
                    ]),

                Tables\Filters\Filter::make('has_subscription')
                    ->label('Hat Abo')
                    ->query(fn(Builder $q) => $q->whereHas('subscriptions', fn($q) => $q->whereIn('status', ['active', 'trial']))),
            ])
            ->actions([
                Tables\Actions\Action::make('suspend')
                    ->label('Sperren')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(User $u) => $u->status === 'active')
                    ->action(fn(User $u) => $u->update(['status' => 'suspended'])),

                Tables\Actions\Action::make('activate')
                    ->label('Aktivieren')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(User $u) => $u->status !== 'active')
                    ->action(fn(User $u) => $u->update(['status' => 'active'])),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

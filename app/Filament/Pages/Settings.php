<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Einstellungen';
    protected static ?string $title = 'Plattform-Einstellungen';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'app_name'              => config('app.name'),
            'trial_days'            => config('app.trial_days', 3),
            'max_login_attempts'    => config('app.max_login_attempts', 5),
            'require_2fa_for_admin' => config('app.require_2fa_for_admin', true),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Allgemein')
                    ->description('Grundlegende Plattform-Einstellungen')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Forms\Components\TextInput::make('app_name')
                            ->label('Plattform-Name')
                            ->required(),

                        Forms\Components\TextInput::make('trial_days')
                            ->label('Trial-Tage (neue User)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(90),
                    ])->columns(2),

                Forms\Components\Section::make('Sicherheit')
                    ->description('Auth & Zugriffskontrolle')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Forms\Components\TextInput::make('max_login_attempts')
                            ->label('Max. Login-Versuche')
                            ->numeric()
                            ->minValue(3)
                            ->maxValue(20),

                        Forms\Components\Toggle::make('require_2fa_for_admin')
                            ->label('2FA für Admins erzwingen')
                            ->helperText('Admins müssen 2FA aktivieren'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Update .env file
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        
        // Update trial_days
        if (isset($data['trial_days'])) {
            $envContent = preg_replace('/^TRIAL_DAYS=.*$/m', "TRIAL_DAYS={$data['trial_days']}", $envContent);
            if (!str_contains($envContent, 'TRIAL_DAYS=')) {
                $envContent .= "\nTRIAL_DAYS={$data['trial_days']}\n";
            }
        }
        
        file_put_contents($envPath, $envContent);
        
        // Clear config cache
        \Artisan::call('config:clear');

        Notification::make()
            ->title('✅ Einstellungen gespeichert')
            ->body('Trial-Tage wurden auf ' . $data['trial_days'] . ' Tage gesetzt.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Speichern')
                ->submit('save'),
        ];
    }
}

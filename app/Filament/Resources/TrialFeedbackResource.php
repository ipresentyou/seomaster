<?php

namespace App\Filament\Resources;

use App\Models\TrialFeedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrialFeedbackResource extends Resource
{
    protected static ?string $model = TrialFeedback::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'SEO Platform';
    protected static ?string $navigationLabel = 'Testphasen-Feedback';
    protected static ?string $modelLabel = 'Testphasen-Feedback';

    public static function canCreate(): bool { return false; }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool { return false; }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('rating')->label('Bewertung (1–5)'),
            Forms\Components\Select::make('reason')->label('Grund')->options(TrialFeedback::REASONS),
            Forms\Components\Textarea::make('comment')->label('Kommentar')->rows(8)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            Tables\Columns\TextColumn::make('created_at')->label('Datum')->dateTime('d.m.Y H:i')->sortable(),
            Tables\Columns\TextColumn::make('subscription.user.email')->label('Nutzer')->searchable(),
            Tables\Columns\TextColumn::make('rating')->label('Bewertung / 5')->sortable(),
            Tables\Columns\TextColumn::make('reason')->label('Grund')->formatStateUsing(fn ($state) => TrialFeedback::REASONS[$state] ?? $state)->wrap(),
            Tables\Columns\TextColumn::make('comment')->label('Kommentar')->limit(80)->wrap(),
        ])->actions([Tables\Actions\ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => TrialFeedbackResource\Pages\ListTrialFeedback::route('/')];
    }
}

<?php

namespace App\Filament\Resources\PayloadResource\RelationManagers;

use App\Models\IntegraHub\PayloadProcessingAttempt;
use App\Utils\Str;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProcessingAttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'processingAttempts';

    protected static ?string $inverseRelationship = 'payload';

    protected static ?string $recordTitleAttribute = 'status';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('payload_processing_attempt.payload_processing_attempts'));
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return Str::formatTitle(__('payload_processing_attempt.payload_processing_attempts'));
    }

    public static function getModelLabel(): ?string
    {
        return Str::formatTitle(__('payload_processing_attempt.payload_processing_attempt'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('payload_processing_attempt.payload_processing_attempts'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('status')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(PayloadProcessingAttempt::STATUS)
                    ->label(Str::formatTitle(__('payload_processing_attempt.status'))),
                TextColumn::make(PayloadProcessingAttempt::CREATED_AT)
                    ->dateTime()
                    ->label(Str::formatTitle(__('payload_processing_attempt.created_at'))),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }
}

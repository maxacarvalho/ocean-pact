<?php

namespace App\Filament\Resources\PayloadResource\RelationManagers;

use App\Enums\PayloadProcessingAttemptsStatusEnum;
use App\Models\PayloadProcessingAttempt;
use App\Utils\Str;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class ProcessingAttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'processingAttempts';
    protected static ?string $inverseRelationship = 'payload';

    protected static ?string $recordTitleAttribute = 'status';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('payload_processing_attempt.payload_processing_attempts'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('payload_processing_attempt.payload_processing_attempt'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('payload_processing_attempt.payload_processing_attempts'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(PayloadProcessingAttempt::STATUS)
                    ->formatStateUsing(fn (?string $state): ?string => $state !== null ? PayloadProcessingAttemptsStatusEnum::from($state)->label : null)
                    ->label(Str::formatTitle(__('payload_processing_attempt.status'))),
                Tables\Columns\TextColumn::make(PayloadProcessingAttempt::MESSAGE)
                    ->label(Str::formatTitle(__('payload_processing_attempt.message'))),
                Tables\Columns\TextColumn::make(PayloadProcessingAttempt::CREATED_AT)
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

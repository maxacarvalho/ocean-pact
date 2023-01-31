<?php

namespace App\Filament\Resources\PayloadResource\RelationManagers;

use App\Enums\PayloadProcessingAttemptsStatusEnum;
use App\Models\PayloadProcessingAttempt;
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
        return __('payload_processing_attempt.PayloadProcessingAttempts');
    }

    public static function getModelLabel(): string
    {
        return __('payload_processing_attempt.PayloadProcessingAttempt');
    }

    public static function getPluralModelLabel(): string
    {
        return __('payload_processing_attempt.PayloadProcessingAttempts');
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
                    ->label(__('payload_processing_attempt.Status')),
                Tables\Columns\TextColumn::make(PayloadProcessingAttempt::MESSAGE)
                    ->label(__('payload_processing_attempt.Message')),
                Tables\Columns\TextColumn::make(PayloadProcessingAttempt::CREATED_AT)
                    ->dateTime()
                    ->label(__('payload_processing_attempt.CreatedAt')),
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

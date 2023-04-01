<?php

namespace App\Filament\Resources;

use App\Enums\PayloadProcessingStatusEnum;
use App\Enums\PayloadStoringStatusEnum;
use App\Filament\Plugins\FilamentSimpleHighlightField\HighlightField;
use App\Filament\Resources\PayloadResource\Pages;
use App\Filament\Resources\PayloadResource\RelationManagers\ProcessingAttemptsRelationManager;
use App\Models\Payload;
use App\Utils\Str;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class PayloadResource extends Resource
{
    protected static ?string $model = Payload::class;

    protected static ?string $navigationIcon = 'heroicon-o-mail';

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('payload.payloads'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('payload.payload'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('payload.payloads'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make(Payload::INTEGRATION_TYPE_ID)
                    ->required()
                    ->relationship('integrationType', 'code')
                    ->label(Str::formatTitle(__('integration_type.integration_type')))
                    ->preload(),
                Forms\Components\Textarea::make(Payload::PAYLOAD)
                    ->required()
                    ->json()
                    ->label(Str::formatTitle(__('payload.payload')))
                    ->columnSpanFull()
                    ->hiddenOn('view'),
                HighlightField::make(Payload::PAYLOAD)
                    ->required()
                    ->json()
                    ->label(Str::formatTitle(__('payload.payload')))
                    ->columnSpanFull()
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('integrationType.code')
                    ->label(Str::formatTitle(__('integration_type.integration_type'))),
                Tables\Columns\TextColumn::make(Payload::STORED_AT)
                    ->dateTime()
                    ->label(Str::formatTitle(__('payload.stored_at'))),
                Tables\Columns\TextColumn::make(Payload::STORING_STATUS)
                    ->formatStateUsing(fn (string $state): string => PayloadStoringStatusEnum::from($state)->label)
                    ->label(Str::formatTitle(__('payload.stored_status'))),
                Tables\Columns\TextColumn::make(Payload::PROCESSED_AT)
                    ->dateTime()
                    ->label(Str::formatTitle(__('payload.processed_at'))),
                Tables\Columns\TextColumn::make(Payload::PROCESSING_STATUS)
                    ->formatStateUsing(fn (?string $state): ?string => $state !== null ? PayloadProcessingStatusEnum::from($state)->label : null)
                    ->label(Str::formatTitle(__('payload.processed_status'))),
                Tables\Columns\TextColumn::make('processing_attempts_count')
                    ->counts('processingAttempts')
                    ->label(Str::formatTitle(__('payload.attempts_count'))),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProcessingAttemptsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayloads::route('/'),
            'view' => Pages\ViewPayload::route('/{record}'),
        ];
    }
}

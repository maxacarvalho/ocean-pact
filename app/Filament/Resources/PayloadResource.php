<?php

namespace App\Filament\Resources;

use App\Enums\PayloadProcessingStatusEnum;
use App\Enums\PayloadStoringStatusEnum;
use App\Filament\Plugins\FilamentSimpleHighlightField\HighlightField;
use App\Filament\Resources\PayloadResource\Pages;
use App\Filament\Resources\PayloadResource\RelationManagers\ProcessingAttemptsRelationManager;
use App\Models\Payload;
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
        return __('payload.Payloads');
    }

    public static function getModelLabel(): string
    {
        return __('payload.Payload');
    }

    public static function getPluralModelLabel(): string
    {
        return __('payload.Payloads');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make(Payload::INTEGRATION_TYPE_ID)
                    ->required()
                    ->relationship('integrationType', 'code')
                    ->label(__('integration_type.IntegrationType'))
                    ->preload(),
                Forms\Components\Textarea::make(Payload::PAYLOAD)
                    ->required()
                    ->json()
                    ->label(__('payload.Payload'))
                    ->columnSpanFull()
                    ->hiddenOn('view'),
                HighlightField::make(Payload::PAYLOAD)
                    ->required()
                    ->json()
                    ->label(__('payload.Payload'))
                    ->columnSpanFull()
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('integrationType.code')
                    ->label(__('integration_type.IntegrationType')),
                Tables\Columns\TextColumn::make(Payload::STORED_AT)
                    ->dateTime()
                    ->label(__('payload.StoredAt')),
                Tables\Columns\TextColumn::make(Payload::STORING_STATUS)
                    ->formatStateUsing(fn (string $state): string => PayloadStoringStatusEnum::from($state)->label)
                    ->label(__('payload.StoredStatus')),
                Tables\Columns\TextColumn::make(Payload::PROCESSED_AT)
                    ->dateTime()
                    ->label(__('payload.ProcessedAt')),
                Tables\Columns\TextColumn::make(Payload::PROCESSING_STATUS)
                    ->formatStateUsing(fn (?string $state): ?string => $state !== null ? PayloadProcessingStatusEnum::from($state)->label : null)
                    ->label(__('payload.ProcessedStatus')),
                Tables\Columns\TextColumn::make('processing_attempts_count')
                    ->counts('processingAttempts')
                    ->label(__('payload.AttemptsCount')),
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

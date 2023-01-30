<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayloadResource\Pages;
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
                    ->label(__('payload.IntegrationType'))
                    ->preload(),
                Forms\Components\Textarea::make(Payload::PAYLOAD)
                    ->required()
                    ->json()
                    ->label(__('payload.Payload'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('integrationType.code')
                    ->label(__('payload.IntegrationType')),
                Tables\Columns\TextColumn::make(Payload::STORED_AT)
                    ->dateTime()
                    ->label(__('payload.StoredAt')),
                Tables\Columns\TextColumn::make(Payload::STORED_STATUS)
                    ->label(__('payload.StoredStatus')),
                Tables\Columns\TextColumn::make(Payload::PROCESSED_AT)
                    ->dateTime()
                    ->label(__('payload.ProcessedAt')),
                Tables\Columns\TextColumn::make(Payload::PROCESSED_STATUS)
                    ->label(__('payload.ProcessedStatus')),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayloads::route('/'),
            'create' => Pages\CreatePayload::route('/create'),
            'view' => Pages\ViewPayload::route('/{record}'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Enums\PayloadProcessingStatusEnum;
use App\Enums\PayloadStoringStatusEnum;
use App\Filament\Plugins\FilamentSimpleHighlightField\HighlightField;
use App\Filament\Resources\PayloadResource\Pages\ListPayloads;
use App\Filament\Resources\PayloadResource\Pages\ViewPayload;
use App\Filament\Resources\PayloadResource\RelationManagers\ProcessingAttemptsRelationManager;
use App\Models\IntegrationType;
use App\Models\Payload;
use App\Utils\Str;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteBulkAction as TableDeleteBulkAction;
use Filament\Tables\Actions\ViewAction as TableViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as TableFilter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Carbon;

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
                Select::make(Payload::INTEGRATION_TYPE_ID)
                    ->label(Str::formatTitle(__('integration_type.integration_type')))
                    ->required()
                    ->relationship('integrationType', 'code')
                    ->preload(),

                Textarea::make(Payload::PAYLOAD)
                    ->label(Str::formatTitle(__('payload.payload')))
                    ->required()
                    ->json()
                    ->columnSpanFull()
                    ->hiddenOn('view'),

                HighlightField::make(Payload::PAYLOAD)
                    ->label(Str::formatTitle(__('payload.payload')))
                    ->required()
                    ->json()
                    ->columnSpanFull()
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Payload::RELATION_INTEGRATION_TYPE.'.'.IntegrationType::CODE)
                    ->label(Str::formatTitle(__('integration_type.integration_type'))),

                TextColumn::make(Payload::STORED_AT)
                    ->label(Str::formatTitle(__('payload.stored_at')))
                    ->dateTime(),

                TextColumn::make(Payload::STORING_STATUS)
                    ->label(Str::formatTitle(__('payload.stored_status')))
                    ->formatStateUsing(fn (string $state): string => PayloadStoringStatusEnum::from($state)->label),

                TextColumn::make(Payload::PROCESSED_AT)
                    ->label(Str::formatTitle(__('payload.processed_at')))
                    ->dateTime(),

                TextColumn::make(Payload::PROCESSING_STATUS)
                    ->label(Str::formatTitle(__('payload.processed_status')))
                    ->formatStateUsing(fn (?string $state): ?string => $state !== null ? PayloadProcessingStatusEnum::from($state)->label : null),

                TextColumn::make('processing_attempts_count')
                    ->label(Str::formatTitle(__('payload.attempts_count')))
                    ->counts('processingAttempts'),
            ])
            ->filters([
                SelectFilter::make(Payload::RELATION_INTEGRATION_TYPE)
                    ->label(Str::formatTitle(__('integration_type.integration_type')))
                    ->relationship('integrationType', 'code'),

                TableFilter::make(Payload::STORED_AT)
                    ->label(Str::formatTitle(__('payload.stored_at')))
                    ->indicateUsing(function (array $data) {
                        $stored_at_from = $data['stored_at_from'];
                        $stored_at_until = $data['stored_at_until'];

                        if ($stored_at_from !== null && $stored_at_until !== null) {
                            return Str::ucfirst(__('payload.received_between', [
                                'from' => Carbon::parse($stored_at_from)->toFormattedDateString(),
                                'until' => Carbon::parse($stored_at_until)->toFormattedDateString(),
                            ]));
                        }

                        if (null !== $stored_at_from) {
                            return Str::ucfirst(__('payload.received_from', [
                                'date' => Carbon::parse($stored_at_from)->toFormattedDateString(),
                            ]));
                        }

                        if (null !== $stored_at_until) {
                            return Str::ucfirst(__('payload.received_until', [
                                'date' => Carbon::parse($stored_at_until)->toFormattedDateString(),
                            ]));
                        }

                        return null;
                    })
                    ->form([
                        DatePicker::make('stored_at_from')
                            ->label(Str::formatTitle(__('payload.stored_at_from')))
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('stored_at_until')
                            ->label(Str::formatTitle(__('payload.stored_at_until')))
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['stored_at_from'],
                                fn (Builder $query, $from): Builder => $query
                                    ->whereDate(Payload::STORED_AT, '>=', $from)
                            )
                            ->when(
                                $data['stored_at_until'],
                                fn (Builder $query, $until): Builder => $query
                                    ->whereDate(Payload::STORED_AT, '<=', $until)
                            );
                    }),

                SelectFilter::make(Payload::STORING_STATUS)
                    ->label(Str::formatTitle(__('payload.stored_status')))
                    ->options(fn () => PayloadStoringStatusEnum::toArray()),

                SelectFilter::make(Payload::PROCESSING_STATUS)
                    ->label(Str::formatTitle(__('payload.processed_status')))
                    ->options(fn () => PayloadProcessingStatusEnum::toArray()),
            ])
            ->actions([
                TableViewAction::make(),
            ])
            ->bulkActions([
                TableDeleteBulkAction::make(),
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
            'index' => ListPayloads::route('/'),
            'view' => ViewPayload::route('/{record}'),
        ];
    }
}

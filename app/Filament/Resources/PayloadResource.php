<?php

namespace App\Filament\Resources;

use App\Enums\IntegraHub\PayloadProcessingStatusEnum;
use App\Enums\IntegraHub\PayloadStoringStatusEnum;
use App\Filament\Resources\PayloadResource\Pages\EditPayload;
use App\Filament\Resources\PayloadResource\Pages\ListPayloads;
use App\Filament\Resources\PayloadResource\RelationManagers\ProcessingAttemptsRelationManager;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\Payload;
use App\Utils\Str;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter as TableFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Carbon;

class PayloadResource extends Resource
{
    protected static ?string $model = Payload::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ViewEntry::make(Payload::PAYLOAD)
                    ->label(Str::formatTitle(__('payload.payload')))
                    ->view('filament-forms::components.prism')
                    ->columnSpanFull(),

                ViewEntry::make(Payload::RESPONSE)
                    ->label(Str::formatTitle(__('payload.response')))
                    ->view('filament-forms::components.prism')
                    ->columnSpanFull(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make(Payload::INTEGRATION_TYPE_ID)
                    ->label(Str::formatTitle(__('integration_type.integration_type')))
                    ->content(fn (Payload $record): string => $record->integrationType->code),

                Select::make(Payload::PROCESSING_STATUS)
                    ->label(Str::formatTitle(__('payload.processed_status')))
                    ->required()
                    ->options(PayloadProcessingStatusEnum::class),

                Textarea::make(Payload::PAYLOAD)
                    ->label(Str::formatTitle(__('payload.payload')))
                    ->required()
                    ->json()
                    ->columnSpanFull()
                    ->hiddenOn(['view', 'edit']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort(Payload::STORED_AT, 'desc')
            ->actions([
                EditAction::make()->label(Str::ucfirst(__('actions.open'))),
            ])
            ->columns([
                TextColumn::make(Payload::RELATION_INTEGRATION_TYPE.'.'.IntegrationType::CODE)
                    ->label(Str::formatTitle(__('integration_type.integration_type'))),

                TextColumn::make(Payload::STORED_AT)
                    ->label(Str::formatTitle(__('payload.stored_at')))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make(Payload::STORING_STATUS)
                    ->label(Str::formatTitle(__('payload.stored_status')))
                    ->sortable(),

                TextColumn::make(Payload::PROCESSED_AT)
                    ->label(Str::formatTitle(__('payload.processed_at')))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make(Payload::PROCESSING_STATUS)
                    ->label(Str::formatTitle(__('payload.processed_status')))
                    ->sortable(),

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
                            return Str::ucfirst(
                                __('payload.received_between', [
                                    'from' => Carbon::parse($stored_at_from)->toFormattedDateString(),
                                    'until' => Carbon::parse($stored_at_until)->toFormattedDateString(),
                                ])
                            );
                        }

                        if (null !== $stored_at_from) {
                            return Str::ucfirst(
                                __('payload.received_from', [
                                    'date' => Carbon::parse($stored_at_from)->toFormattedDateString(),
                                ])
                            );
                        }

                        if (null !== $stored_at_until) {
                            return Str::ucfirst(
                                __('payload.received_until', [
                                    'date' => Carbon::parse($stored_at_until)->toFormattedDateString(),
                                ])
                            );
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
                    ->options(PayloadStoringStatusEnum::class),

                SelectFilter::make(Payload::PROCESSING_STATUS)
                    ->label(Str::formatTitle(__('payload.processed_status')))
                    ->options(PayloadProcessingStatusEnum::class),

                TableFilter::make('payload_filter')
                    ->label('Payload')
                    ->indicateUsing(function (array $data) {
                        if (filled($data['payload'])) {
                            return 'Payload';
                        }

                        return null;
                    })
                    ->form([
                        TextInput::make('payload'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['payload'],
                                fn (Builder $query, $payload): Builder => $query
                                    ->where(Payload::PAYLOAD, 'like', "%$payload%")
                            );
                    }),

            ], layout: FiltersLayout::AboveContentCollapsible);
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
            'edit' => EditPayload::route('/{record}/edit'),
        ];
    }
}

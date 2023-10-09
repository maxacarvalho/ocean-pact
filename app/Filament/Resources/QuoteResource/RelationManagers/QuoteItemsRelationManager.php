<?php

namespace App\Filament\Resources\QuoteResource\RelationManagers;

use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Rules\PercentageMaxValueRule;
use App\Tables\Columns\MaskedInputColumn;
use App\Utils\Str;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * @property Quote $ownerRecord
 */
class QuoteItemsRelationManager extends RelationManager
{
    protected static string $relationship = Quote::RELATION_ITEMS;

    protected static ?string $recordTitleAttribute = QuoteItem::ITEM;

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('quote_item.quote_items'));
    }

    public static function getModelLabel(): ?string
    {
        return Str::formatTitle(__('quote_item.quote_item'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('quote_item.quote_items'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make(QuoteItem::ITEM)
                    ->label(Str::formatTitle(__('quote_item.item')))
                    ->content(fn (Model|QuoteItem $record) => $record->item),

                Placeholder::make(QuoteItem::PRODUCT_ID)
                    ->label(Str::formatTitle(__('quote_item.product_id')))
                    ->content(fn (Model|QuoteItem $record) => $record->product->code),

                Placeholder::make(QuoteItem::DESCRIPTION)
                    ->label(Str::formatTitle(__('quote_item.description')))
                    ->content(fn (Model|QuoteItem $record) => $record->description)
                    ->columnSpanFull(),

                FileUpload::make(QuoteItem::SELLER_IMAGE)
                    ->label(Str::formatTitle(__('quote_item.seller_image')))
                    ->hidden(Auth::user()->isBuyer())
                    ->disk('s3')
                    ->visibility('private')
                    ->downloadable()
                    ->image()
                    ->saveUploadedFileUsing(static function (BaseFileUpload $component, TemporaryUploadedFile $file, QuoteItem $record) {
                        try {
                            if (! $file->exists()) {
                                return null;
                            }
                        } catch (UnableToCheckFileExistence $exception) {
                            return null;
                        }

                        return $file->storeAs(
                            "quotes/{$record->quote_id}/quote_items/{$record->id}/seller_image",
                            $component->getUploadedFileNameForStorage($file),
                            $component->getDiskName(),
                        );
                    }),

                FileUpload::make(QuoteItem::BUYER_IMAGE)
                    ->label(Str::formatTitle(__('quote_item.buyer_image')))
                    ->hidden(Auth::user()->isSeller())
                    ->disk('s3')
                    ->visibility('private')
                    ->downloadable()
                    ->image()
                    ->saveUploadedFileUsing(static function (BaseFileUpload $component, TemporaryUploadedFile $file, QuoteItem $record) {
                        try {
                            if (! $file->exists()) {
                                return null;
                            }
                        } catch (UnableToCheckFileExistence $exception) {
                            return null;
                        }

                        return $file->storeAs(
                            "quotes/{$record->quote_id}/quote_items/{$record->id}/buyer_image",
                            $component->getUploadedFileNameForStorage($file),
                            $component->getDiskName(),
                        );
                    }),

                Placeholder::make(QuoteItem::MEASUREMENT_UNIT)
                    ->label(Str::formatTitle(__('quote_item.measurement_unit')))
                    ->content(fn (Model|QuoteItem $record) => $record->measurement_unit),

                Placeholder::make(QuoteItem::QUANTITY)
                    ->label(Str::formatTitle(__('quote_item.quantity')))
                    ->content(fn (Model|QuoteItem $record) => $record->quantity),

                Grid::make(3)
                    ->schema([
                        TextInput::make(QuoteItem::IPI)
                            ->label(Str::formatTitle(__('quote_item.ipi')))
                            ->mask(function (Model|Quote $record) {
                                if ('BRL' === $record->currency) {
                                    return RawJs::make('$money($input, \',\', \'.\')');
                                }

                                return RawJs::make('$money($input)');
                            })
                            ->required(fn (Get $get) => $get(QuoteItem::SHOULD_BE_QUOTED))
                            ->rules([
                                new PercentageMaxValueRule(100),
                            ])
                            ->formatStateUsing(function (string $state, Model|Quote $record) {
                                if ('BRL' === $record->currency) {
                                    return str_replace('.', ',', $state);
                                }

                                return $state;
                            })
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace(',', '.', $state)),

                        TextInput::make(QuoteItem::ICMS)
                            ->label(Str::formatTitle(__('quote_item.icms')))
                            ->required(fn (Get $get) => $get(QuoteItem::SHOULD_BE_QUOTED))
                            ->mask(function (Model|Quote $record) {
                                if ('BRL' === $record->currency) {
                                    return RawJs::make('$money($input, \',\', \'.\')');
                                }

                                return RawJs::make('$money($input)');
                            })
                            ->rules([
                                new PercentageMaxValueRule(100),
                            ])
                            ->formatStateUsing(function (string $state, Model|Quote $record) {
                                if ('BRL' === $record->currency) {
                                    return str_replace('.', ',', $state);
                                }

                                return $state;
                            })
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace(',', '.', $state)),

                        TextInput::make(QuoteItem::UNIT_PRICE)
                            ->label(Str::formatTitle(__('quote_item.unit_price')))
                            ->required(fn (Get $get) => $get(QuoteItem::SHOULD_BE_QUOTED))
                            ->default(0)
                            ->mask(function (Model|Quote $record) {
                                if ('BRL' === $record->currency) {
                                    return RawJs::make('$money($input, \',\', \'.\')');
                                }

                                return RawJs::make('$money($input)');
                            }),

                        TextInput::make(QuoteItem::DELIVERY_IN_DAYS)
                            ->label(Str::formatTitle(__('quote_item.delivery_in_days')))
                            ->rules(['required', 'min:0', 'numeric'])
                            ->type('number')
                            ->default(0)
                            ->required(fn (Get $get) => $get(QuoteItem::SHOULD_BE_QUOTED)),
                    ])
                    ->columnSpanFull(),

                Checkbox::make(QuoteItem::SHOULD_BE_QUOTED)
                    ->label(Str::formatTitle(__('quote_item.should_be_quoted')))
                    ->default(true)
                    ->reactive(),

                Textarea::make(QuoteItem::COMMENTS)
                    ->label(Str::formatTitle(__('quote_item.comments')))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(QuoteItem::RELATION_QUOTE))
            ->columns([
                TextColumn::make(QuoteItem::ITEM)
                    ->label(Str::formatTitle(__('quote_item.item'))),

                TextColumn::make(QuoteItem::RELATION_PRODUCT.'.'.Product::CODE)
                    ->label(Str::formatTitle(__('quote_item.product'))),

                TextColumn::make(QuoteItem::DESCRIPTION)
                    ->label(Str::formatTitle(__('quote_item.description'))),

                ImageColumn::make(QuoteItem::SELLER_IMAGE)
                    ->label(Str::formatTitle(__('quote_item.seller_image')))
                    ->alignCenter()
                    ->disk('s3')
                    ->visibility('private')
                    ->url(function (QuoteItem $record) {
                        if ($record->seller_image) {
                            return Storage::disk('s3')->temporaryUrl($record->seller_image, now()->addMinutes(5));
                        }

                        return null;
                    })
                    ->openUrlInNewTab(),

                ImageColumn::make(QuoteItem::BUYER_IMAGE)
                    ->label(Str::formatTitle(__('quote_item.buyer_image')))
                    ->alignCenter()
                    ->disk('s3')
                    ->visibility('private')
                    ->url(function (QuoteItem $record) {
                        if ($record->buyer_image) {
                            return Storage::disk('s3')->temporaryUrl($record->buyer_image, now()->addMinutes(5));
                        }

                        return null;
                    })
                    ->openUrlInNewTab(),

                TextColumn::make(QuoteItem::MEASUREMENT_UNIT)
                    ->label(Str::formatTitle(__('quote_item.measurement_unit'))),

                TextColumn::make(QuoteItem::QUANTITY)
                    ->label(Str::formatTitle(__('quote_item.quantity'))),

                MaskedInputColumn::make(QuoteItem::ICMS)
                    ->label(Str::formatTitle(__('quote_item.icms')))
                    ->rules(['required'])
                    ->state(function (Model|QuoteItem $record) {
                        return number_format($record->icms, 2, ',', '.');
                    })
                    ->updateStateUsing(function (string $state, Model|QuoteItem $record): string {
                        $asFloat = (float) str_replace(',', '.', $state);

                        $record->update([
                            QuoteItem::ICMS => $asFloat,
                        ]);

                        return number_format($asFloat, 2, ',', '.');
                    })
                    ->mask(function (Model|Quote $record) {
                        if ('BRL' === $record->currency) {
                            return RawJs::make('$money($input, \',\', \'.\')');
                        }

                        return RawJs::make('$money($input)');
                    })
                    ->extraAttributes(['class' => 'w-32'])
                    ->disabled(fn (Model|QuoteItem $record): bool => $record->cannotBeResponded()),

                MaskedInputColumn::make(QuoteItem::IPI)
                    ->label(Str::formatTitle(__('quote_item.ipi')))
                    ->rules(['required'])
                    ->state(function (Model|QuoteItem $record) {
                        return number_format($record->ipi, 2, ',', '.');
                    })
                    ->updateStateUsing(function (string $state, Model|QuoteItem $record): string {
                        $asFloat = (float) str_replace(',', '.', $state);

                        $record->update([
                            QuoteItem::IPI => $asFloat,
                        ]);

                        return number_format($asFloat, 2, ',', '.');
                    })
                    ->mask(function (Model|Quote $record) {
                        if ('BRL' === $record->currency) {
                            return RawJs::make('$money($input, \',\', \'.\')');
                        }

                        return RawJs::make('$money($input)');
                    })
                    ->extraAttributes(['class' => 'w-32'])
                    ->disabled(fn (Model|QuoteItem $record): bool => $record->cannotBeResponded()),

                MaskedInputColumn::make(QuoteItem::UNIT_PRICE)
                    ->label(Str::formatTitle(__('quote_item.unit_price')))
                    ->rules(['required'])
                    ->mask(function (Model|Quote $record) {
                        if ('BRL' === $record->currency) {
                            return RawJs::make('$money($input, \',\', \'.\')');
                        }

                        return RawJs::make('$money($input)');
                    })
                    ->disabled(fn (Model|QuoteItem $record): bool => $record->cannotBeResponded()),

                TextInputColumn::make(QuoteItem::DELIVERY_IN_DAYS)
                    ->label(Str::formatTitle(__('quote_item.delivery_in_days')))
                    ->rules(['required', 'min:0', 'numeric'])
                    ->type('number')
                    ->default(0)
                    ->disabled(fn (Model|QuoteItem $record): bool => $record->cannotBeResponded()),

                ToggleColumn::make(QuoteItem::SHOULD_BE_QUOTED)
                    ->label(Str::formatTitle(__('quote_item.should_be_quoted')))
                    ->disabled(fn (Model|QuoteItem $record): bool => $record->cannotBeResponded()),

                TextColumn::make(QuoteItem::STATUS)
                    ->label(Str::formatTitle(__('quote_item.status')))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                TableEditAction::make()
                    ->using(function (QuoteItem $record, array $data) {
                        if (
                            null !== $record->seller_image
                            &&
                            (isset($data[QuoteItem::SELLER_IMAGE]) && (null === $data[QuoteItem::SELLER_IMAGE] || $record->seller_image !== $data[QuoteItem::SELLER_IMAGE]))
                        ) {
                            Storage::disk('s3')->delete($record->seller_image);
                        }

                        if (
                            null !== $record->buyer_image
                            &&
                            (isset($data[QuoteItem::BUYER_IMAGE]) && (null === $data[QuoteItem::BUYER_IMAGE] || $record->buyer_image !== $data[QuoteItem::BUYER_IMAGE]))
                        ) {
                            Storage::disk('s3')->delete($record->buyer_image);
                        }

                        $record->update($data);

                        return $record;
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }
}

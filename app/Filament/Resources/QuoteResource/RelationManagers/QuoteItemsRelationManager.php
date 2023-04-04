<?php

namespace App\Filament\Resources\QuoteResource\RelationManagers;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Utils\Str;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class QuoteItemsRelationManager extends RelationManager
{
    protected static string $relationship = Quote::RELATION_ITEMS;

    protected static ?string $recordTitleAttribute = QuoteItem::ITEM;

    public static function getNavigationLabel(): string
    {
        return Str::formatTitle(__('quote_item.quote_items'));
    }

    public static function getModelLabel(): string
    {
        return Str::formatTitle(__('quote_item.quote_item'));
    }

    public static function getPluralModelLabel(): string
    {
        return Str::formatTitle(__('quote_item.quote_items'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(QuoteItem::PRODUCT_ID)
                    ->label(Str::formatTitle(__('quote_item.product_id')))
                    ->relationship(QuoteItem::RELATION_PRODUCT, Product::DESCRIPTION)
                    ->required(),

                TextInput::make(QuoteItem::DESCRIPTION)
                    ->label(Str::formatTitle(__('quote_item.description')))
                    ->required(),

                TextInput::make(QuoteItem::MEASUREMENT_UNIT)
                    ->label(Str::formatTitle(__('quote_item.measurement_unit')))
                    ->required(),

                TextInput::make(QuoteItem::ITEM)
                    ->label(Str::formatTitle(__('quote_item.item')))
                    ->required(),

                TextInput::make(QuoteItem::QUANTITY)
                    ->label(Str::formatTitle(__('quote_item.quantity')))
                    ->numeric()
                    ->required(),

                TextInput::make(QuoteItem::UNIT_PRICE)
                    ->label(Str::formatTitle(__('quote_item.unit_price')))
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->mask(fn (TextInput\Mask $mask) => $mask
                        ->patternBlocks([
                            'money' => fn (Mask $mask) => $mask
                                ->numeric()
                                ->thousandsSeparator('.')
                                ->decimalSeparator(',')
                                ->decimalPlaces(2)
                                ->signed(true)
                                ->padFractionalZeros()
                                ->normalizeZeros(false),
                        ])
                        ->pattern('R$money')
                        ->lazyPlaceholder(false)
                    ),

                Checkbox::make(QuoteItem::SHOULD_BE_QUOTED)
                    ->label(Str::formatTitle(__('quote_item.should_be_quoted')))
                    ->default(true),

                Textarea::make(QuoteItem::COMMENTS)
                    ->label(Str::formatTitle(__('quote_item.comments')))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(QuoteItem::DESCRIPTION)
                    ->label(Str::formatTitle(__('quote_item.description'))),

                TextColumn::make(QuoteItem::MEASUREMENT_UNIT)
                    ->label(Str::formatTitle(__('quote_item.measurement_unit'))),

                TextColumn::make(QuoteItem::ITEM)
                    ->label(Str::formatTitle(__('quote_item.item'))),

                TextColumn::make(QuoteItem::QUANTITY)
                    ->label(Str::formatTitle(__('quote_item.quantity'))),

                TextColumn::make(QuoteItem::UNIT_PRICE)
                    ->label(Str::formatTitle(__('quote_item.unit_price')))
                    ->formatStateUsing(function (?string $state): ?string {
                        $money = new Money($state, new Currency('BRL'));

                        return $money->format();
                    }),

                IconColumn::make(QuoteItem::SHOULD_BE_QUOTED)->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data[QuoteItem::UNIT_PRICE] = self::makeMoney($data[QuoteItem::UNIT_PRICE])->getAmount();

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data) {
                        $data[QuoteItem::UNIT_PRICE] = self::makeMoney($data[QuoteItem::UNIT_PRICE])->formatSimple();

                        return $data;
                    })
                    ->mutateFormDataUsing(function (array $data) {
                        $amount = number_format((float) $data[QuoteItem::UNIT_PRICE], 2, '.', ',');

                        $data[QuoteItem::UNIT_PRICE] = self::makeMoney($amount)->getAmount();

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    private static function makeMoney(mixed $amount): Money
    {
        return new Money($amount, new Currency('BRL'));
    }
}

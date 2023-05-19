<?php

namespace App\Filament\Resources\QuoteResource\RelationManagers;

use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Tables\Columns\CurrencyInputColumn;
use App\Utils\Str;
use Brick\Money\Money;
use Closure;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

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
                Placeholder::make(QuoteItem::ITEM)
                    ->label(Str::formatTitle(__('quote_item.item')))
                    ->content(fn (Model|QuoteItem $record) => $record->item),

                Placeholder::make(QuoteItem::PRODUCT_ID)
                    ->label(Str::formatTitle(__('quote_item.product_id')))
                    ->content(fn (Model|QuoteItem $record) => $record->product->code),

                Placeholder::make(QuoteItem::DESCRIPTION)
                    ->label(Str::formatTitle(__('quote_item.description')))
                    ->content(fn (Model|QuoteItem $record) => $record->description),

                Placeholder::make(QuoteItem::MEASUREMENT_UNIT)
                    ->label(Str::formatTitle(__('quote_item.measurement_unit')))
                    ->content(fn (Model|QuoteItem $record) => $record->measurement_unit),

                Grid::make(3)
                    ->schema([
                        Placeholder::make(QuoteItem::QUANTITY)
                            ->label(Str::formatTitle(__('quote_item.quantity')))
                            ->content(fn (Model|QuoteItem $record) => $record->quantity),

                        TextInput::make(QuoteItem::UNIT_PRICE)
                            ->label(Str::formatTitle(__('quote_item.unit_price')))
                            ->required(fn (Closure $get) => $get(QuoteItem::SHOULD_BE_QUOTED))
                            ->default(0)
                            ->mask(fn (TextInput\Mask $mask) => $mask
                                ->patternBlocks([
                                    'money' => fn (Mask $mask) => $mask
                                        ->numeric()
                                        ->decimalPlaces(2)
                                        ->decimalSeparator(',')
                                        ->mapToDecimalSeparator([','])
                                        ->signed(true)
                                        ->normalizeZeros()
                                        ->padFractionalZeros()
                                        ->thousandsSeparator('.'),
                                ])
                                ->pattern('R$money')
                                ->lazyPlaceholder(false)
                            ),

                        DatePicker::make(QuoteItem::DELIVERY_DATE)
                            ->label(Str::formatTitle(__('quote_item.delivery_date')))
                            ->displayFormat('d/m/Y')
                            ->required(fn (Closure $get) => $get(QuoteItem::SHOULD_BE_QUOTED)),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(QuoteItem::ITEM)
                    ->label(Str::formatTitle(__('quote_item.item'))),

                TextColumn::make(QuoteItem::RELATION_PRODUCT.'.'.Product::CODE)
                    ->label(Str::formatTitle(__('quote_item.product'))),

                TextColumn::make(QuoteItem::DESCRIPTION)
                    ->label(Str::formatTitle(__('quote_item.description'))),

                TextColumn::make(QuoteItem::MEASUREMENT_UNIT)
                    ->label(Str::formatTitle(__('quote_item.measurement_unit'))),

                TextColumn::make(QuoteItem::QUANTITY)
                    ->label(Str::formatTitle(__('quote_item.quantity'))),

                TextColumn::make(QuoteItem::UNIT_PRICE)
                    ->label(Str::formatTitle(__('quote_item.unit_price')))
                    ->formatStateUsing(function (?int $state): ?string {
                        if ($state === null) {
                            return null;
                        }

                        $money = Money::ofMinor($state, 'BRL');

                        return $money->formatTo('pt_BR');
                    }),

                CurrencyInputColumn::make('total_price')
                    ->label(Str::formatTitle(__('quote_item.total_price')))
                    ->getStateUsing(function (Model|QuoteItem $record): string {
                        try {
                            $totalPrice = $record->quantity * $record->unit_price;
                            $money = Money::ofMinor($totalPrice, 'BRL');

                            return $money->formatTo('pt_BR');
                        } catch (Exception $exception) {
                            return $exception->getMessage();
                        }
                    }),

                TextColumn::make(QuoteItem::DELIVERY_DATE)
                    ->label(Str::formatTitle(__('quote_item.delivery_date')))
                    ->date('d/m/Y'),

                ToggleColumn::make(QuoteItem::SHOULD_BE_QUOTED)
                    ->label(Str::formatTitle(__('quote_item.should_be_quoted'))),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                TableEditAction::make()
                    ->mutateRecordDataUsing(function (array $data) {
                        $money = Money::ofMinor($data[QuoteItem::UNIT_PRICE], 'BRL');

                        $data[QuoteItem::UNIT_PRICE] = $money->formatTo('pt_BR');

                        return $data;
                    })
                    ->mutateFormDataUsing(function (array $data) {
                        try {
                            $amount = Money::of($data[QuoteItem::UNIT_PRICE], 'BRL');

                            $data[QuoteItem::UNIT_PRICE] = $amount->getMinorAmount()->toInt();
                        } catch (Exception $exception) {
                            unset($data[QuoteItem::UNIT_PRICE]);
                        }

                        return $data;
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }

    protected function getTableQuery(): Builder|Relation
    {
        return parent::getTableQuery()->with(QuoteItem::RELATION_QUOTE);
    }
}

<?php

namespace App\Livewire\QuoteAnalysisPanel;

use App\Data\QuotesPortal\PredictedPurchaseRequestData;
use App\Models\QuotesPortal\PredictedPurchaseRequest as PredictedPurchaseRequestModel;
use App\Models\QuotesPortal\Product;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Models\QuotesPortal\Supplier;
use App\Utils\Money;
use App\Utils\Str;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Locked;
use Livewire\Component;
use stdClass;
use Throwable;

/**
 * @property Form $form
 */
class PredictedPurchaseRequest extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public ?array $data = [];

    #[Locked]
    public int $companyId;
    #[Locked]
    public string $quoteNumber;

    public function mount(int $companyId, string $quoteNumber): void
    {
        $this->companyId = $companyId;
        $this->quoteNumber = $quoteNumber;
        $this->form->fill();
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.quote-analysis-panel.predicted-purchase-request');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('lower_price')
                    ->label(Str::ucfirst(__('quote_analysis_panel.quick_actions_panel_lower_price')))
                    ->default(true),
                Toggle::make('lower_eta')
                    ->label(Str::ucfirst(__('quote_analysis_panel.quick_actions_panel_lower_eta'))),

                Toggle::make('last_price')
                    ->label(Str::ucfirst(__('quote_analysis_panel.quick_actions_panel_last_price'))),
                Toggle::make('necessity')
                    ->label(Str::ucfirst(__('quote_analysis_panel.quick_actions_panel_necessity'))),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(Str::title(__('quote_analysis_panel.predicted_purchase_request')))
            ->query(
                PredictedPurchaseRequestModel::query()
                    ->where(PredictedPurchaseRequestModel::COMPANY_ID, $this->companyId)
                    ->where(PredictedPurchaseRequestModel::QUOTE_NUMBER, $this->quoteNumber)
            )
            ->columns([
                TextColumn::make(PredictedPurchaseRequestModel::RELATION_SUPPLIER.'.'.Supplier::BUSINESS_NAME)
                    ->label(Str::title(__('predicted_purchase_request.supplier'))),

                TextColumn::make(PredictedPurchaseRequestModel::RELATION_PRODUCT.'.'.Product::DESCRIPTION)
                    ->label(Str::title(__('predicted_purchase_request.product')))
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),

                TextColumn::make(PredictedPurchaseRequestModel::DELIVERY_DATE)
                    ->label(Str::title(__('predicted_purchase_request.delivery_date')))
                    ->date('d/m/Y'),

                TextColumn::make(PredictedPurchaseRequestModel::PRICE)
                    ->label(Str::title(__('predicted_purchase_request.price')))
                    ->formatStateUsing(function (Money $state, Model|PredictedPurchaseRequestModel $record) {
                        return $record->price->getFormattedAmount();
                    })
                    ->badge()
                    ->color(function (Money $state, Model|PredictedPurchaseRequestModel $record): string {
                        if ($record->last_price->getMinorAmount() === 0) {
                            return 'gray';
                        }

                        if ($record->last_price->getMinorAmount() > $state->getMinorAmount()) {
                            return 'success';
                        }

                        if ($record->last_price->getMinorAmount() < $state->getMinorAmount()) {
                            return 'danger';
                        }

                        return 'gray';
                    })
                    ->summarize(
                        Summarizer::make()
                            ->using(function (Builder $query) {
                                $items = $query
                                    ->get()
                                    ->map(function (stdClass $record) {
                                        $data = Json::decode($record->price);

                                        $record->price = Money::ofMinor(
                                            currency: $data['currency'],
                                            amount: $data['amount'],
                                        );

                                        return $record;
                                    });

                                $sum = $items->reduce(function (?int $carry, stdClass $record) {
                                    return $carry + $record->price->getMinorAmount();
                                }, 0);

                                $currency = $items->first()->price->getCurrency();

                                return Money::ofMinor(
                                    currency: $currency,
                                    amount: $sum,
                                )->getFormattedAmount();
                            })
                    ),

                TextColumn::make(PredictedPurchaseRequestModel::LAST_PRICE)
                    ->label(Str::title(__('predicted_purchase_request.last_price')))
                    ->formatStateUsing(function (Money $state, Model|PredictedPurchaseRequestModel $record) {
                        return $record->last_price->getFormattedAmount();
                    })
                    ->summarize(
                        Summarizer::make()
                            ->using(function (Builder $query) {
                                $items = $query
                                    ->get()
                                    ->map(function (stdClass $record) {
                                        $data = Json::decode($record->last_price);

                                        $record->last_price = Money::ofMinor(
                                            currency: $data['currency'],
                                            amount: $data['amount'],
                                        );

                                        return $record;
                                    });

                                $sum = $items->reduce(function (?int $carry, stdClass $record) {
                                    return $carry + $record->last_price->getMinorAmount();
                                }, 0);

                                $currency = $items->first()->last_price->getCurrency();

                                return Money::ofMinor(
                                    currency: $currency,
                                    amount: $sum,
                                )->getFormattedAmount();
                            })
                    ),

                TextColumn::make('savings')
                    ->label(Str::title(__('predicted_purchase_request.savings')))
                    ->state(function (Model|PredictedPurchaseRequestModel $record) {
                        try {
                            return 100 - (($record->price->getMinorAmount() / $record->last_price->getMinorAmount()) * 100);
                        } catch (Throwable) {
                            return 0;
                        }
                    })
                    ->formatStateUsing(function (float $state): string {
                        return number_format(
                            num: $state,
                            decimals: 2,
                            decimal_separator: ','
                        ).'%';
                    })
                    ->badge()
                    ->color(function (float $state): string {
                        if ($state > 0) {
                            return 'success';
                        }

                        if ($state < 0) {
                            return 'danger';
                        }

                        return 'gray';
                    })
                    ->summarize(
                        Summarizer::make()
                            ->using(function (Builder $query) {
                                $items = $query
                                    ->get()
                                    ->map(function (stdClass $record) {
                                        $data = Json::decode($record->last_price);

                                        $record->last_price = Money::ofMinor(
                                            currency: $data['currency'],
                                            amount: $data['amount'],
                                        );

                                        $data = Json::decode($record->price);

                                        $record->price = Money::ofMinor(
                                            currency: $data['currency'],
                                            amount: $data['amount'],
                                        );

                                        return $record;
                                    });

                                $sumPrice = $items->reduce(function (?int $carry, stdClass $record) {
                                    return $carry + $record->price->getMinorAmount();
                                }, 0);

                                $sumLastPrice = $items->reduce(function (?int $carry, stdClass $record) {
                                    return $carry + $record->last_price->getMinorAmount();
                                }, 0);

                                if ($sumLastPrice === 0) {
                                    return '0%';
                                }

                                return number_format(
                                    num: 100 - (($sumPrice / $sumLastPrice) * 100),
                                    decimals: 2,
                                    decimal_separator: ','
                                ).'%';
                            })
                    ),

                TextColumn::make(PredictedPurchaseRequestModel::NECESSITY_DATE)
                    ->label(Str::title(__('predicted_purchase_request.necessity_date')))
                    ->date('d/m/Y'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function update(): void
    {
        $allQuoteItems = QuoteItem::query()
            ->with([QuoteItem::RELATION_QUOTE, QuoteItem::RELATION_PRODUCT])
            ->whereHas(QuoteItem::RELATION_QUOTE, function (Builder $query): void {
                $query
                    ->where(Quote::COMPANY_ID, $this->companyId)
                    ->where(Quote::QUOTE_NUMBER, $this->quoteNumber);
            })
            ->where(QuoteItem::UNIT_PRICE, '>', 0)
            ->orderBy(QuoteItem::ITEM)
            ->get();

        $uniqueQuoteItems = $allQuoteItems->pluck('item')->unique()->values();

        PredictedPurchaseRequestModel::query()
            ->where(PredictedPurchaseRequestModel::QUOTE_NUMBER, $this->quoteNumber)
            ->where(PredictedPurchaseRequestModel::COMPANY_ID, $this->companyId)
            ->delete();

        foreach ($uniqueQuoteItems as $uniqueQuoteItem) {
            /** @var QuoteItem[]|Collection $quoteItems */
            $quoteItems = $allQuoteItems->where('item', $uniqueQuoteItem);

            /** @var Product $product */
            $product = $quoteItems->first()->product;

            /** @var QuoteItem $quoteItemWithTheLowestPrice */
            $quoteItemWithTheLowestPrice = $quoteItems->reduce(function (?QuoteItem $carry, QuoteItem $quoteItem) {
                if ($carry === null) {
                    return $quoteItem;
                }

                if ($quoteItem->unit_price->getMinorAmount()->toInt() < $carry->unit_price->getMinorAmount()->toInt()) {
                    return $quoteItem;
                }

                return $carry;
            });

            $data = PredictedPurchaseRequestData::from([
                PredictedPurchaseRequestModel::COMPANY_ID => $this->companyId,
                PredictedPurchaseRequestModel::QUOTE_NUMBER => $this->quoteNumber,
                PredictedPurchaseRequestModel::QUOTE_ID => $quoteItemWithTheLowestPrice->quote_id,
                PredictedPurchaseRequestModel::SUPPLIER_ID => $quoteItemWithTheLowestPrice->quote->supplier_id,
                PredictedPurchaseRequestModel::PRODUCT_ID => $product->id,
                PredictedPurchaseRequestModel::ITEM => $uniqueQuoteItem,
                PredictedPurchaseRequestModel::QUOTE_ITEM_ID => $quoteItemWithTheLowestPrice->id,
                PredictedPurchaseRequestModel::DELIVERY_DATE => $quoteItemWithTheLowestPrice->updated_at->addDays(
                    $quoteItemWithTheLowestPrice->delivery_in_days
                ),
                PredictedPurchaseRequestModel::PRICE => [
                    'currency' => $quoteItemWithTheLowestPrice->unit_price->getCurrency(),
                    'amount' => $quoteItemWithTheLowestPrice->unit_price->getMinorAmount()->toInt(),
                ],
                PredictedPurchaseRequestModel::LAST_PRICE => $quoteItemWithTheLowestPrice->product->last_price,
                PredictedPurchaseRequestModel::NECESSITY_DATE => now(),
            ]);

            PredictedPurchaseRequestModel::query()->create($data->toArray());
        }
    }
}

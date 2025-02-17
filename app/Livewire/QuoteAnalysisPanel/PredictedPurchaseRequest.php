<?php

namespace App\Livewire\QuoteAnalysisPanel;

use App\Actions\QuotesPortal\AcceptPredictedPurchaseRequestAction;
use App\Actions\QuotesPortal\AddNewSupplierToQuoteAction;
use App\Data\QuotesPortal\PredictedPurchaseRequestData;
use App\Enums\QuotesPortal\QuoteItemStatusEnum;
use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Exceptions\QuotesPortal\MissingPredictedPurchaseRequestItemsException;
use App\Exceptions\QuotesPortal\PredictedPurchaseRequestAlreadyAcceptedException;
use App\Models\QuotesPortal\PredictedPurchaseRequest as PredictedPurchaseRequestModel;
use App\Models\QuotesPortal\Product;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Models\QuotesPortal\Supplier;
use App\Utils\Money;
use App\Utils\Str;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use stdClass;
use Throwable;

/**
 * @property Form $form
 */
class PredictedPurchaseRequest extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public ?array $data = [];

    #[Locked]
    public int $companyId;

    #[Locked]
    public string $quoteNumber;

    public bool $isQuoteBuyerOwner;

    public int $predictedPurchaseRequestCount = 0;

    public ?string $cannotAcceptPredictedPurchaseRequestModalContent = null;

    public bool $isReadOnly = false;

    public function mount(int $companyId, string $quoteNumber, bool $isQuoteBuyerOwner, bool $isReadOnly): void
    {
        $this->companyId = $companyId;
        $this->quoteNumber = $quoteNumber;
        $this->isQuoteBuyerOwner = $isQuoteBuyerOwner;
        $this->predictedPurchaseRequestCount = $this->getTableQuery()->count();
        $this->isReadOnly = $isReadOnly;

        $this->form->fill();

        $selectedQuoteItems = $this->getTableQuery()
            ->get()
            ->pluck('quote_item_id');

        $this->dispatch('predictedPurchaseRequestLoaded', selectedQuoteItems: $selectedQuoteItems);
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.quote-analysis-panel.predicted-purchase-request');
    }

    #[On('predictedPurchaseRequestItemToggled')]
    public function onPredictedPurchaseRequestItemToggled(int $quoteItemId, string $item, int $productId, $state): void
    {
        if ($state === false) {
            $this->getTableQuery()->where(PredictedPurchaseRequestModel::QUOTE_ITEM_ID, '=', $quoteItemId)->delete();
        }

        if ($state === true) {
            $this->getTableQuery()
                ->where(PredictedPurchaseRequestModel::ITEM, '=', $item)
                ->where(PredictedPurchaseRequestModel::PRODUCT_ID, '=', $productId)
                ->delete();

            /** @var QuoteItem $quoteItem */
            $quoteItem = QuoteItem::query()
                ->with([QuoteItem::RELATION_QUOTE, QuoteItem::RELATION_PRODUCT])
                ->findOrFail($quoteItemId);

            $data = PredictedPurchaseRequestData::from([
                PredictedPurchaseRequestModel::COMPANY_ID => $this->companyId,
                PredictedPurchaseRequestModel::QUOTE_NUMBER => $this->quoteNumber,
                PredictedPurchaseRequestModel::BUYER_ID => $quoteItem->quote->buyer_id,
                PredictedPurchaseRequestModel::QUOTE_ID => $quoteItem->quote_id,
                PredictedPurchaseRequestModel::SUPPLIER_ID => $quoteItem->quote->supplier_id,
                PredictedPurchaseRequestModel::PRODUCT_ID => $quoteItem->product->id,
                PredictedPurchaseRequestModel::ITEM => $quoteItem->item,
                PredictedPurchaseRequestModel::QUOTE_ITEM_ID => $quoteItem->id,
                PredictedPurchaseRequestModel::DELIVERY_DATE => $quoteItem->updated_at->addDays(
                    $quoteItem->delivery_in_days
                ),
                PredictedPurchaseRequestModel::PRICE => [
                    'currency' => $quoteItem->unit_price->getCurrency(),
                    'amount' => $quoteItem->unit_price->getMinorAmount()->toInt(),
                ],
                PredictedPurchaseRequestModel::LAST_PRICE => $quoteItem->product->last_price,
                PredictedPurchaseRequestModel::NECESSITY_DATE => now(),
            ]);

            PredictedPurchaseRequestModel::query()->create($data->toArray());
        }

        $selectedQuoteItems = $this->getTableQuery()
            ->get()
            ->pluck('quote_item_id');

        $this->dispatch('predictedPurchaseRequestLoaded', selectedQuoteItems: $selectedQuoteItems);
    }

    public function acceptPredictedPurchaseRequestAction(): Action
    {
        return Action::make('acceptPredictedPurchaseRequestAction')
            ->label(Str::ucfirst(__('quote_analysis_panel.finish_quote_selected_products')))
            ->disabled($this->predictedPurchaseRequestCount === 0)
            ->requiresConfirmation()
            ->action(function () {
                try {
                    (new AcceptPredictedPurchaseRequestAction)->handle(
                        $this->companyId,
                        $this->quoteNumber
                    );

                    $this->isReadOnly = true;
                    $this->dispatch('predictedPurchaseRequestAccepted');
                } catch (MissingPredictedPurchaseRequestItemsException|PredictedPurchaseRequestAlreadyAcceptedException $exception) {
                    $this->cannotAcceptPredictedPurchaseRequestModalContent = $exception->getMessage();
                    $this->dispatch('open-modal', id: 'cannot-accept-predicted-purchase-request-modal');
                } catch (Throwable $exception) {
                    $this->cannotAcceptPredictedPurchaseRequestModalContent = Str::ucfirst(__('quote_analysis_panel.cannot_finalize_quote_unknown_error'));
                    $this->dispatch('open-modal', id: 'cannot-accept-predicted-purchase-request-modal');
                }
            });
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
                Select::make('supplier')
                    ->label(Str::ucfirst(__('quote_analysis_panel.quick_actions_panel_suppliers')))
                    ->options(
                        Supplier::query()
                            ->whereHas(Supplier::RELATION_QUOTES, function (Builder $query): void {
                                $query->where(Quote::TABLE_NAME.'.'.Quote::QUOTE_NUMBER, $this->quoteNumber);
                            })
                            ->orderBy(Supplier::NAME)
                            ->pluck(Supplier::TABLE_NAME.'.'.Supplier::NAME, Supplier::TABLE_NAME.'.'.Supplier::ID)
                            ->toArray()
                    )
                    ->searchable()
                    ->multiple(),
            ])
            ->statePath('data')
            ->columns(['sm' => 2, 'md' => 4]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(Str::title(__('quote_analysis_panel.predicted_purchase_request')))
            ->query(
                $this->getTableQuery()
            )
            ->columns([
                TextColumn::make(PredictedPurchaseRequestModel::ITEM)
                    ->label(Str::title(__('quote_item.item'))),

                TextColumn::make(PredictedPurchaseRequestModel::RELATION_SUPPLIER.'.'.Supplier::NAME)
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
                        return number_format(num: $state, decimals: 2, decimal_separator: ',').'%';
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

                                $savings = 100 - (($sumPrice / $sumLastPrice) * 100);

                                return number_format(num: $savings, decimals: 2, decimal_separator: ',').'%';
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

    public function loadProductsAction(): Action
    {
        return Action::make('loadProductsAction')
            ->label(Str::ucfirst(__('quote_analysis_panel.quick_actions_panel_load_products')))
            ->submit('loadProducts');
    }

    public function loadProducts(): void
    {
        $filtering = $this->form->getState();

        if (! $filtering['lower_price'] && ! $filtering['lower_eta'] && ! $filtering['last_price'] && ! $filtering['necessity']) {
            Notification::make()
                ->title(Str::ucfirst(__('quote_analysis_panel.quick_actions_panel_required')))
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        $quoteIds = Quote::query()
            ->where(Quote::COMPANY_ID, '=', $this->companyId)
            ->where(Quote::QUOTE_NUMBER, '=', $this->quoteNumber)
            ->where(Quote::STATUS, '=', QuoteStatusEnum::RESPONDED)
            ->pluck(Quote::ID);

        $allQuoteItems = QuoteItem::query()
            ->with([QuoteItem::RELATION_QUOTE, QuoteItem::RELATION_PRODUCT])
            ->whereIn(QuoteItem::QUOTE_ID, $quoteIds)
            ->where(QuoteItem::SHOULD_BE_QUOTED, '=', true)
            ->whereNot(QuoteItem::STATUS, '=', QuoteItemStatusEnum::REJECTED)
            ->where(QuoteItem::UNIT_PRICE, '>', 0)
            ->when($filtering['lower_price'], function (Builder $query): void {
                $query->orderBy(QuoteItem::UNIT_PRICE);
            })
            ->when($filtering['lower_eta'], function (Builder $query): void {
                $query->orderBy(QuoteItem::DELIVERY_IN_DAYS, 'DESC');
            })
            ->when($filtering['last_price'], function (Builder $query): void {
                $query->join(Quote::TABLE_NAME, Quote::TABLE_NAME.'.'.Quote::ID, '=', QuoteItem::TABLE_NAME.'.'.QuoteItem::QUOTE_ID)
                    ->join(Product::TABLE_NAME, QuoteItem::TABLE_NAME.'.'.QuoteItem::PRODUCT_ID, '=', Product::TABLE_NAME.'.'.Product::ID)
                    ->select(Product::TABLE_NAME.'.*', QuoteItem::TABLE_NAME.'.*')
                    ->addSelect(DB::raw('json_extract('.Product::TABLE_NAME.'.'.Product::LAST_PRICE.', "$.amount") AS last_price_int'))
                    ->orderBy('last_price_int', 'asc');
            })
            ->when($filtering['necessity'], function (Builder $query): void {
                // aguardando campo data necessidade
            })
            ->when(count($filtering['supplier']) > 0, function (Builder $query) use ($filtering): void {
                $query->join(Quote::TABLE_NAME, Quote::TABLE_NAME.'.'.Quote::ID, '=', QuoteItem::TABLE_NAME.'.'.QuoteItem::QUOTE_ID)
                    ->whereIn(Quote::TABLE_NAME.'.'.Quote::SUPPLIER_ID, $filtering['supplier']);
            })
            ->get();

        $uniqueQuoteItems = $allQuoteItems->pluck('item')->unique()->sort()->values();

        PredictedPurchaseRequestModel::query()
            ->where(PredictedPurchaseRequestModel::QUOTE_NUMBER, $this->quoteNumber)
            ->where(PredictedPurchaseRequestModel::COMPANY_ID, $this->companyId)
            ->delete();

        foreach ($uniqueQuoteItems as $uniqueQuoteItem) {
            /** @var QuoteItem[]|Collection $quoteItems */
            $quoteItems = $allQuoteItems->where('item', $uniqueQuoteItem);

            /** @var Product $product */
            $product = $quoteItems->first()->product;

            /** @var QuoteItem $quoteItemFiltered */
            $quoteItemFiltered = $quoteItems->reduce(function (?QuoteItem $carry, QuoteItem $quoteItem) use ($filtering) {
                if ($carry === null) {
                    return $quoteItem;
                }

                if ($filtering['lower_price'] && $quoteItem->unit_price->getMinorAmount()->toInt() < $carry->unit_price->getMinorAmount()->toInt()) {
                    return $quoteItem;
                }

                if ($filtering['lower_eta'] && $quoteItem->delivery_in_days < $carry->delivery_in_days ||
                    ($quoteItem->delivery_in_days == $carry->delivery_in_days && $quoteItem->unit_price->getMinorAmount()->toInt() < $carry->unit_price->getMinorAmount()->toInt())) {
                    return $quoteItem;
                }

                return $carry;
            });

            $data = PredictedPurchaseRequestData::from([
                PredictedPurchaseRequestModel::COMPANY_ID => $this->companyId,
                PredictedPurchaseRequestModel::QUOTE_NUMBER => $this->quoteNumber,
                PredictedPurchaseRequestModel::BUYER_ID => $quoteItemFiltered->quote->buyer_id,
                PredictedPurchaseRequestModel::QUOTE_ID => $quoteItemFiltered->quote_id,
                PredictedPurchaseRequestModel::SUPPLIER_ID => $quoteItemFiltered->quote->supplier_id,
                PredictedPurchaseRequestModel::PRODUCT_ID => $product->id,
                PredictedPurchaseRequestModel::ITEM => $uniqueQuoteItem,
                PredictedPurchaseRequestModel::QUOTE_ITEM_ID => $quoteItemFiltered->id,
                PredictedPurchaseRequestModel::DELIVERY_DATE => $quoteItemFiltered->updated_at->addDays(
                    $quoteItemFiltered->delivery_in_days
                ),
                PredictedPurchaseRequestModel::PRICE => [
                    'currency' => $quoteItemFiltered->unit_price->getCurrency(),
                    'amount' => $quoteItemFiltered->unit_price->getMinorAmount()->toInt(),
                ],
                PredictedPurchaseRequestModel::LAST_PRICE => $quoteItemFiltered->product->last_price,
                PredictedPurchaseRequestModel::NECESSITY_DATE => now(),
            ]);

            PredictedPurchaseRequestModel::query()->create($data->toArray());
        }

        $this->predictedPurchaseRequestCount = $this->getTableQuery()->count();

        $selectedQuoteItems = PredictedPurchaseRequestModel::query()
            ->where(PredictedPurchaseRequestModel::QUOTE_NUMBER, $this->quoteNumber)
            ->where(PredictedPurchaseRequestModel::COMPANY_ID, $this->companyId)
            ->pluck('quote_item_id')
            ->toArray();

        $this->dispatch('predictedPurchaseRequestLoaded', selectedQuoteItems: $selectedQuoteItems);
    }

    public function finishQuote()
    {
        //
    }

    public function addNewSupplierToQuoteAction(): Action
    {
        return Action::make('addNewSupplierToQuoteAction')
            ->label(Str::ucfirst(__('quote_analysis_panel.add_new_supplier')))
            ->requiresConfirmation()
            ->form([
                Select::make('newSupplier')
                    ->label(Str::ucfirst(__('supplier.name')))
                    ->required()
                    ->selectablePlaceholder(false)
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Supplier::query()
                        ->whereNotIn(Supplier::ID, $this->getIncludedSupplierIds())
                        ->where(function (\Illuminate\Database\Eloquent\Builder $query) use ($search) {
                            $query->where(Supplier::NAME, 'like', '%'.$search.'%')
                                ->orWhere(Supplier::BUSINESS_NAME, 'like', '%'.$search.'%')
                                ->orWhere(Supplier::CODE, 'like', '%'.$search.'%');
                        })
                        ->orderBy(Supplier::NAME)
                        ->pluck(Supplier::NAME, Supplier::ID)
                        ->toArray()
                    )
                    ->options(
                        fn () => Supplier::query()
                            ->whereNotIn(Supplier::ID, $this->getIncludedSupplierIds())
                            ->orderBy(Supplier::NAME)
                            ->pluck(Supplier::NAME, Supplier::ID)
                    ),
            ])
            ->action(function (array $data, AddNewSupplierToQuoteAction $addNewSupplierToQuoteAction): void {
                $newSupplierId = $data['newSupplier'];

                $addNewSupplierToQuoteAction->handle(
                    $this->companyId,
                    $this->quoteNumber,
                    $newSupplierId
                );

                $this->dispatch('newSupplierAddedToQuote');
            });
    }

    public function addNewSupplierToQuote()
    {
        //
    }

    private function getIncludedSupplierIds(): array
    {
        return Quote::query()
            ->where(Quote::COMPANY_ID, $this->companyId)
            ->where(Quote::QUOTE_NUMBER, $this->quoteNumber)
            ->pluck(Quote::SUPPLIER_ID)
            ->toArray();
    }

    private function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return PredictedPurchaseRequestModel::query()
            ->where(PredictedPurchaseRequestModel::COMPANY_ID, $this->companyId)
            ->where(PredictedPurchaseRequestModel::QUOTE_NUMBER, $this->quoteNumber);
    }
}

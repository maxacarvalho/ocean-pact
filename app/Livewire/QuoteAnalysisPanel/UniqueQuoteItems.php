<?php

namespace App\Livewire\QuoteAnalysisPanel;

use App\Models\QuotesPortal\Product;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Utils\Money;
use App\Utils\Str;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Locked;
use Livewire\Component;

class UniqueQuoteItems extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    #[Locked]
    public string $quoteNumber;
    #[Locked]
    public int $companyId;

    public function mount(int $companyId, string $quoteNumber): void
    {
        $this->companyId = $companyId;
        $this->quoteNumber = $quoteNumber;
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.quote-analysis-panel.unique-quote-items');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                return QuoteItem::query()
                    ->select([
                        QuoteItem::ITEM.' AS id',
                        QuoteItem::PRODUCT_ID,
                        QuoteItem::ITEM,
                        QuoteItem::DESCRIPTION,
                    ])
                    ->groupBy([
                        QuoteItem::ITEM,
                        QuoteItem::PRODUCT_ID,
                        QuoteItem::DESCRIPTION,
                    ])
                    ->with([
                        QuoteItem::RELATION_PRODUCT,
                    ])
                    ->whereHas(QuoteItem::RELATION_QUOTE, function (Builder $query): void {
                        $query
                            ->where(Quote::COMPANY_ID, $this->companyId)
                            ->where(Quote::QUOTE_NUMBER, $this->quoteNumber);
                    });
            })
            ->queryStringIdentifier("unique-quote-items-{$this->companyId}-{$this->quoteNumber}")
            ->defaultSort(QuoteItem::ITEM)
            ->columns([
                TextColumn::make(QuoteItem::ITEM)
                    ->label(Str::title(__('quote_item.item')))
                    ->searchable(),

                TextColumn::make(QuoteItem::RELATION_PRODUCT.'.'.Product::CODE)
                    ->label(Str::title(__('product.code')))
                    ->searchable(),

                TextColumn::make(QuoteItem::DESCRIPTION)
                    ->label(Str::title(__('quote_item.description')))
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),

                TextColumn::make(QuoteItem::RELATION_PRODUCT.'.'.Product::LAST_PRICE)
                    ->label(Str::title(__('product.last_price')))
                    ->formatStateUsing(fn (Money $state): string => $state->getFormattedAmount()),

                TextColumn::make(QuoteItem::RELATION_PRODUCT.'.'.Product::SMALLEST_PRICE)
                    ->label(Str::title(__('product.smallest_price')))
                    ->formatStateUsing(fn (Money $state): string => $state->getFormattedAmount()),

                TextColumn::make(QuoteItem::RELATION_PRODUCT.'.'.Product::SMALLEST_ETA)
                    ->label(Str::title(__('product.smallest_eta'))),
            ])
            ->filters([
                //
            ]);
    }
}

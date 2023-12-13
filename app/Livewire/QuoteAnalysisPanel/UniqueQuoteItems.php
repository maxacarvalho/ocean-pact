<?php

namespace App\Livewire\QuoteAnalysisPanel;

use App\Models\QuotesPortal\Product;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
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
                    ->distinct()
                    ->select([
                        QuoteItem::ITEM.' AS id',
                        QuoteItem::PRODUCT_ID,
                        QuoteItem::ITEM,
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
                    ->label(__('quote_item.item'))
                    ->searchable(),

                TextColumn::make(QuoteItem::RELATION_PRODUCT.'.'.Product::CODE)
                    ->label(__('product.code'))
                    ->searchable(),

                TextColumn::make(QuoteItem::DESCRIPTION)
                    ->label(__('quote_item.description'))
                    ->searchable(),
            ])
            ->filters([
                //
            ]);
    }
}

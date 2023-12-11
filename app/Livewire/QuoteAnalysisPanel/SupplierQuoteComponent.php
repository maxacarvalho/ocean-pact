<?php

namespace App\Livewire\QuoteAnalysisPanel;

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

class SupplierQuoteComponent extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    #[Locked]
    public int $quoteId;

    public string $supplierName;

    public function mount(int $quoteId): void
    {
        $this->quoteId = $quoteId;

        /** @var Quote $quote */
        $quote = Quote::query()
            ->with([
                Quote::RELATION_SUPPLIER,
            ])
            ->findOrFail($quoteId);

        $this->supplierName = $quote->supplier->name;
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.quote-analysis-panel.supplier-quote-component');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading($this->supplierName)
            ->paginated(false)
            ->query(fn (): Builder => QuoteItem::query()->where(QuoteItem::QUOTE_ID, $this->quoteId))
            ->defaultSort(QuoteItem::ITEM)
            ->columns([
                TextColumn::make(QuoteItem::ITEM)
                    ->label(__('quote_item.item')),

                TextColumn::make(QuoteItem::DESCRIPTION)
                    ->label(__('quote_item.description')),
            ]);
    }
}

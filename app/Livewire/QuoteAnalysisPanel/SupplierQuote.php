<?php

namespace App\Livewire\QuoteAnalysisPanel;

use App\Actions\QuotesPortal\RequestNewOfferAction;
use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Utils\Str;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SupplierQuote extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Quote $quote;
    #[Locked]
    public int $quoteId;
    public QuoteStatusEnum $quoteStatus;
    public bool $isQuoteBuyerOwner;
    public string $supplierName;

    public function mount(int $quoteId, bool $isQuoteBuyerOwner): void
    {
        $this->quoteId = $quoteId;
        $this->isQuoteBuyerOwner = $isQuoteBuyerOwner;

        $this->quote = $this->getQuote($quoteId);

        $this->quoteStatus = $this->quote->status;
        $this->supplierName = $this->quote->supplier->name;
    }

    public function render(): View|Application|Factory
    {
        return view('livewire.quote-analysis-panel.supplier-quote');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading($this->supplierName)
            ->description(Str::ucfirst(__('quote.quote_version', ['version' => $this->quote->proposal_number])))
            ->query(fn (): Builder => QuoteItem::query()->where(QuoteItem::QUOTE_ID, $this->quoteId))
            ->queryStringIdentifier("supplier-quote-{$this->quoteId}")
            ->defaultSort(QuoteItem::ITEM)
            ->columns([
                TextColumn::make(QuoteItem::ITEM)
                    ->label(Str::title(__('quote_item.item'))),

                TextColumn::make(QuoteItem::DESCRIPTION)
                    ->label(Str::title(__('quote_item.description')))
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),

                TextColumn::make(QuoteItem::UNIT_PRICE)
                    ->label(Str::title(__('quote_item.unit_price'))),

                TextColumn::make(QuoteItem::DELIVERY_IN_DAYS)
                    ->label(__('quote_analysis_panel.eta'))
                    ->formatStateUsing(function (int $state): string {
                        return Carbon::now()->addDays($state)->format('d/m/Y');
                    }),

                CheckboxColumn::make('is_selected')->label(__('quote_analysis_panel.is_selected')),
            ]);
    }

    public function canRequestNewProposal(): bool
    {
        return !$this->quoteStatus->equals(QuoteStatusEnum::PROPOSAL);
    }

    public function requestNewOfferConfirmModal(): void
    {
        $this->dispatch('open-modal', id: "request-new-offer-modal-{$this->quoteId}");
    }

    public function requestNewOfferExecute(): void
    {
        (new RequestNewOfferAction())->handle($this->quoteId);

        Quote::query()
            ->where(Quote::ID, $this->quoteId)
            ->update([
                Quote::STATUS => QuoteStatusEnum::PROPOSAL,
            ]);

        $this->quoteStatus = QuoteStatusEnum::PROPOSAL;

        $this->dispatch('close-modal', id: "request-new-offer-modal-{$this->quoteId}");
    }

    public function requestContact()
    {

    }

    private function getQuote(int $quoteId): Quote
    {
        /** @var Quote $quote */
        $quote = Quote::query()
            ->with([
                Quote::RELATION_SUPPLIER,
            ])
            ->findOrFail($quoteId);

        return $quote;
    }
}

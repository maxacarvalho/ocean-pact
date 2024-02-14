<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Filament\Resources\QuoteResource;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Utils\Str;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class QuoteAnalysisPanel extends Page
{
    public int $companyId;
    public string $quoteNumber;
    #[Locked]
    public array $quoteIds = [];
    public Collection|array $quoteItems = [];

    protected static string $resource = QuoteResource::class;
    protected static string $view = 'filament.resources.quote-resource.pages.quote-analysis-panel';

    public function mount(int $companyId, string $quoteNumber): void
    {
        $this->companyId = $companyId;
        $this->quoteNumber = $quoteNumber;
        $this->quoteIds = $this->getQuoteIds();
        $this->quoteItems = $this->getQuoteItems();
    }

    #[On('newSupplierAddedToQuote')]
    public function newSupplierAddedToQuote(): void
    {
        $this->quoteIds = $this->getQuoteIds();
    }

    #[On('newQuoteProposalRequested')]
    public function newQuoteProposalRequested(): void
    {
        $this->quoteIds = $this->getQuoteIds();
    }

    public function getTitle(): Htmlable|string
    {
        return Str::title(
            __('quote_analysis_panel.quote_analysis_panel_page_title', ['quote_number' => $this->quoteNumber])
        );
    }

    public function isQuoteBuyerOwner(): bool
    {
        if (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()) {
            return true;
        }

        $quoteBuyerIds = Quote::query()
            ->where(Quote::COMPANY_ID, $this->companyId)
            ->where(Quote::QUOTE_NUMBER, $this->quoteNumber)
            ->pluck(Quote::BUYER_ID)
            ->toArray();

        return in_array(auth()->user()->id, $quoteBuyerIds, true);
    }

    private function getQuoteIds(): array
    {
        return Quote::query()
            ->select(Quote::ID)
            ->where(Quote::COMPANY_ID, $this->companyId)
            ->where(Quote::QUOTE_NUMBER, $this->quoteNumber)
            ->whereIn(Quote::STATUS, [
                QuoteStatusEnum::PROPOSAL,
                QuoteStatusEnum::PENDING,
                QuoteStatusEnum::RESPONDED,
                QuoteStatusEnum::ANALYZED,
            ])
            ->pluck(Quote::ID)
            ->toArray();
    }

    private function getQuoteItems(): Collection|array
    {
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
            })
            ->get();
    }
}

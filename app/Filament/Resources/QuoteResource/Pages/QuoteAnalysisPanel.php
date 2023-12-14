<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Utils\Str;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Locked;

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

    public function getTitle(): Htmlable|string
    {
        return Str::title(
            __('quote_analysis_panel.quote_analysis_panel_page_title', ['quote_number' => $this->quoteNumber])
        );
    }

    private function getQuoteIds(): array
    {
        return Quote::query()
            ->select('id')
            ->where(Quote::COMPANY_ID, $this->companyId)
            ->where(Quote::QUOTE_NUMBER, $this->quoteNumber)
            ->pluck('id')
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

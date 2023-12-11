<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\QuotesPortal\Quote;
use App\Utils\Str;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Locked;

class QuoteAnalysisPanel extends Page
{
    public string $quoteNumber;

    #[Locked]
    public array $quoteIds = [];

    protected static string $resource = QuoteResource::class;

    protected static string $view = 'filament.resources.quote-resource.pages.quote-analysis-panel';

    public function mount(string $quoteNumber): void
    {
        $this->quoteNumber = $quoteNumber;
        $this->quoteIds = $this->getQuoteIds();
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
            ->where(Quote::QUOTE_NUMBER, $this->quoteNumber)
            ->pluck('id')
            ->toArray();
    }
}

<x-filament-panels::page>
    <livewire:quote-analysis-panel.unique-quote-items
        :company-id="$this->companyId"
        :quote-number="$this->quoteNumber"
        :is-quote-buyer-owner="$this->isQuoteBuyerOwner()"
    />

    <livewire:quote-analysis-panel.predicted-purchase-request
        :company-id="$this->companyId"
        :quote-number="$this->quoteNumber"
        :is-quote-buyer-owner="$this->isQuoteBuyerOwner()"
    />

    <div class="overflow-x-auto flex space-x-4 p-1">
        @foreach($this->quoteIds as $quoteId)
            <livewire:quote-analysis-panel.supplier-quote
                :wire:key="$quoteId"
                :quote-id="$quoteId"
                :is-quote-buyer-owner="$this->isQuoteBuyerOwner()"
            />
        @endforeach
    </div>

    @if ($this->isQuoteBuyerOwner())
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mt-4">
            <div class="w-full flex p-2 justify-end">
                <x-filament::button wire:click="endQuoteWithSelectedProducts">
                    {{ \App\Utils\Str::ucfirst(__('quote_analysis_panel.finish_quote_selected_products')) }}
                </x-filament::button>
            </div>
        </div>
    @endif
</x-filament-panels::page>

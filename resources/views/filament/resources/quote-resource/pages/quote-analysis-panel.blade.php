@use('App\Utils\Str')

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
        :is-read-only="$this->isReadOnly"
    />

    <div class="overflow-x-auto flex space-x-4 p-1">
        @foreach($this->quoteIds as $quoteId)
            <livewire:quote-analysis-panel.supplier-quote
                :wire:key="$quoteId"
                :quote-id="$quoteId"
                :is-quote-buyer-owner="$this->isQuoteBuyerOwner()"
                :is-read-only="$this->isReadOnly"
            />
        @endforeach
    </div>
</x-filament-panels::page>

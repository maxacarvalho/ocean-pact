<x-filament-panels::page>
    <livewire:quote-analysis-panel.unique-quote-items :company-id="$this->companyId" :quote-number="$this->quoteNumber" />

    <div class="overflow-x-auto flex space-x-4 p-1">
        @foreach($this->quoteIds as $quoteId)
            <livewire:quote-analysis-panel.supplier-quote-component :quote-id="$quoteId" :wire:key="$quoteId" />
        @endforeach
    </div>
</x-filament-panels::page>

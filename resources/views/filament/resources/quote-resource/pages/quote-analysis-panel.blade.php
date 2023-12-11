<x-filament-panels::page>
    <livewire:quote-analysis-panel.list-items-component :quote-number="$this->quoteNumber" />

    <x-filament::grid
        :default="1"
        :sm="2"
        class="gap-6"
    >
        @foreach($this->quoteIds as $quoteId)
            <livewire:quote-analysis-panel.supplier-quote-component :quote-id="$quoteId" :wire:key="$quoteId" />
        @endforeach
    </x-filament::grid>
</x-filament-panels::page>

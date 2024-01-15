<div class="grid gap-4">
    <div>
        {{ $this->table }}
    </div>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="w-full flex p-2 justify-between">
            <x-filament::button wire:click="requestNewOffer">
                {{ \App\Utils\Str::ucfirst(__('quote_analysis_panel.request_new_offer')) }}
            </x-filament::button>

            <x-filament::button wire:click="requestContact">
                {{ \App\Utils\Str::ucfirst(__('quote_analysis_panel.request_contact')) }}
            </x-filament::button>
        </div>
    </div>
</div>

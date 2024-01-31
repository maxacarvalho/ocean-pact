@use('App\Utils\Str')

<div class="grid gap-4">
    <div>
        {{ $this->table }}
    </div>

    @if ($this->isQuoteBuyerOwner)
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="w-full flex p-2 justify-center gap-4">
                {{ $this->requestNewOfferAction() }}

                <x-filament::button wire:click="requestContact">
                    {{ Str::ucfirst(__('quote_analysis_panel.request_contact')) }}
                </x-filament::button>
            </div>
        </div>
    @endif
</div>

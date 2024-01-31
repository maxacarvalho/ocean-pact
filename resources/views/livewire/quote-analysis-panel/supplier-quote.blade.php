@use('App\Utils\Str')

<div class="grid gap-4">
    <div>
        {{ $this->table }}
    </div>

    @if ($this->isQuoteBuyerOwner)
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="w-full flex p-2 justify-center gap-4">
                <x-filament::button
                    wire:click="requestNewOfferConfirmModal"
                    :disabled="!$this->canRequestNewProposal()"
                >
                    {{ Str::ucfirst(__('quote_analysis_panel.request_new_offer')) }}
                </x-filament::button>

                <x-filament::modal
                    id="request-new-offer-modal-{{ $this->quoteId }}"
                    alignment="center"
                    icon="fas-triangle-exclamation"
                    footer-actions-alignment="center"
                    width="md"
                >
                    <x-slot name="heading">
                        {{ Str::ucfirst(__('quote_analysis_panel.request_new_offer_modal_header')) }}
                    </x-slot>

                    <x-slot name="description">
                        {{ Str::ucfirst(__('quote_analysis_panel.request_new_offer_modal_description')) }}
                    </x-slot>

                    <x-slot name="footerActions">
                        <x-filament::button x-on:click="close()" color="gray">
                            {{ Str::ucfirst(__('quote_analysis_panel.request_new_offer_modal_cancel_btn')) }}
                        </x-filament::button>

                        <x-filament::button wire:click="requestNewOfferExecute">
                            {{ Str::ucfirst(__('quote_analysis_panel.request_new_offer_modal_confirm_btn')) }}
                        </x-filament::button>
                    </x-slot>
                </x-filament::modal>

                <x-filament::button wire:click="requestContact">
                    {{ Str::ucfirst(__('quote_analysis_panel.request_contact')) }}
                </x-filament::button>
            </div>
        </div>
    @endif
</div>

@use('App\Utils\Str')

<div class="grid gap-4">
    <div>
        {{ $this->table }}
    </div>

    @if ($this->isQuoteBuyerOwner)
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="w-full flex p-2 justify-center gap-4">
                <x-filament::button
                    wire:click="requestNewProposalConfirmationModal"
                    :disabled="!$this->canRequestNewProposal()"
                >
                    {{ Str::ucfirst(__('quote_analysis_panel.request_new_proposal')) }}
                </x-filament::button>

                <x-filament::modal
                    id="request-new-proposal-confirmation-modal-{{ $this->quoteId }}"
                    alignment="center"
                    icon="fas-triangle-exclamation"
                    footer-actions-alignment="center"
                    width="md"
                >
                    <x-slot name="heading">
                        {{ Str::ucfirst(__('quote_analysis_panel.request_new_proposal_modal_header')) }}
                    </x-slot>

                    <x-slot name="description">
                        {{ Str::ucfirst(__('quote_analysis_panel.request_new_proposal_modal_description')) }}
                    </x-slot>

                    <x-slot name="footerActions">
                        <x-filament::button x-on:click="close()" color="gray">
                            {{ Str::ucfirst(__('quote_analysis_panel.request_new_proposal_modal_cancel_btn')) }}
                        </x-filament::button>

                        <x-filament::button wire:click="requestNewProposal">
                            {{ Str::ucfirst(__('quote_analysis_panel.request_new_proposal_modal_confirm_btn')) }}
                        </x-filament::button>
                    </x-slot>
                </x-filament::modal>

                <x-filament::modal
                    id="request-contact-modal-{{ $this->quoteId }}"
                    alignment="center"
                    icon="fas-envelope"
                    footer-actions-alignment="center"
                    width="md"
                >
                    <x-slot name="heading">
                        {{ Str::ucfirst(__('quote_analysis_panel.request_contact_modal_header')) }}
                    </x-slot>

                    <x-slot name="description">
                        <form wire:submit="requestContact">
                            {{ $this->contactRequestForm }}
                        </form>
                    </x-slot>

                    <x-slot name="footerActions">
                        <x-filament::button x-on:click="close()" color="gray">
                            {{ Str::ucfirst(__('quote_analysis_panel.request_contact_modal_cancel_btn')) }}
                        </x-filament::button>

                        <x-filament::button wire:click="requestContact">
                            {{ Str::ucfirst(__('quote_analysis_panel.request_contact_modal_send_btn')) }}
                        </x-filament::button>
                    </x-slot>
                </x-filament::modal>

                <x-filament::button wire:click="openRequestContactModal">
                    {{ Str::ucfirst(__('quote_analysis_panel.request_contact')) }}
                </x-filament::button>
            </div>
        </div>
    @endif
</div>

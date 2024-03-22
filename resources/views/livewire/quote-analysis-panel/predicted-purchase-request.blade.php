@use('App\Utils\Str')

<div class="flex flex-col gap-4">
    @if ($this->isQuoteBuyerOwner && $isReadOnly === false)
        <div class="bg-white shadow-sm rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="p-4 sm:px-6">
                <div>
                    <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white pb-4">
                        {{ Str::title(__('quote_analysis_panel.quick_actions_panel_title')) }}
                    </h3>

                    <form wire:submit="loadProducts" class="grid gap-6">
                        <div>
                            {{ $this->form }}
                        </div>

                        <div>
                            {{ $this->loadProductsAction() }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div>
        {{ $this->table }}

        @if ($this->isQuoteBuyerOwner && $isReadOnly === false)
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mt-4">
                <div class="w-full flex p-2 justify-center gap-4">
                    {{ $this->acceptPredictedPurchaseRequestAction() }}

                    <x-filament::modal
                        id="cannot-accept-predicted-purchase-request-modal"
                        alignment="center"
                        icon="fas-triangle-exclamation"
                        icon-color="danger"
                        width="md"
                    >
                        <x-slot name="heading">
                            {{ Str::ucfirst(__('quote_analysis_panel.cannot_finalize_quote')) }}
                        </x-slot>

                        <x-slot name="description">
                            {{ $this->cannotAcceptPredictedPurchaseRequestModalContent }}
                        </x-slot>

                        <x-slot name="footer"><p></p></x-slot>
                    </x-filament::modal>

                    {{ $this->addNewSupplierToQuoteAction() }}
                </div>
            </div>
        @endif
    </div>
</div>

<x-filament::page
    :widget-data="['record' => $record]"
    :class="\Illuminate\Support\Arr::toCssClasses([
        'filament-resources-edit-record-page',
        'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'filament-resources-record-' . $record->getKey(),
    ])"
>
    @capture($form)
    <x-filament::form wire:submit.prevent="save">
        {{ $this->form }}

        {{--<x-filament::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />--}}
    </x-filament::form>
    @endcapture

    @php
        $relationManagers = $this->getRelationManagers();
    @endphp

    @if ((! $this->hasCombinedRelationManagerTabsWithForm()) || (! count($relationManagers)))
        {{ $form() }}
    @endif

    @if (count($relationManagers))
        @if (! $this->hasCombinedRelationManagerTabsWithForm())
            <x-filament::hr />
        @endif

        <x-filament::resources.relation-managers
            :active-manager="$activeRelationManager"
            :form-tab-label="$this->getFormTabLabel()"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        >
            @if ($this->hasCombinedRelationManagerTabsWithForm())
                <x-slot name="form">
                    {{ $form() }}
                </x-slot>
            @endif
        </x-filament::resources.relation-managers>
    @endif

    <x-filament::hr />

    {{--<div>
        <div class="text-base font-normal text-gray-900">{{ \App\Utils\Str::ucfirst(__('quote.total')) }}</div>
        <div class="mt-1 flex items-baseline justify-between md:block lg:flex">
            <div class="flex items-baseline text-2xl font-semibold">
                {{ $this->getTotal() }}
            </div>
        </div>
    </div>

    <x-filament::hr />--}}

    <div class="rounded-md border border-warning-300 bg-warning-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <x-heroicon-s-exclamation-circle class="h-5 w-5 text-warning-400" />
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-warning-800">
                    {{ Str::ucfirst(__('quote.form_save_action_confirmation')) }}
                </h3>
            </div>
        </div>
    </div>

    @if($missingItemsUnitPriceOrDeliveryDate)
        <div class="rounded-md border border-danger-300 bg-danger-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-s-exclamation-circle class="h-5 w-5 text-danger-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-danger-800">
                        {{ Str::ucfirst(__('quote.please_fill_the_unit_price_and_delivery_date_for_all_items')) }}
                    </h3>
                </div>
            </div>
        </div>
    @endif

    <x-filament-support::button
        color="danger"
        :dark-mode="config('filament.dark_mode')"
        tag="button"
        type="button"
        wire:click="sendQuote"
    >
        {{ \App\Utils\Str::formatTitle(__('quote.form_save_action_label')) }}
    </x-filament-support::button>

    <x-filament-support::button
        color="secondary"
        :dark-mode="config('filament.dark_mode')"
        tag="button"
        type="a"
        wire:click="cancel"
    >
        {{ __('filament::resources/pages/edit-record.form.actions.cancel.label') }}
    </x-filament-support::button>
</x-filament::page>

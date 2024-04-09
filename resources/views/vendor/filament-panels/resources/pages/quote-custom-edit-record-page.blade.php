<x-filament-panels::page
    @class([
        'fi-resource-edit-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>
    @capture($form)
        <x-filament-panels::form
            :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
            wire:submit="save"
        >
            {{ $this->form }}
        </x-filament-panels::form>
    @endcapture

    @php
        $relationManagers = $this->getRelationManagers();
        $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();
    @endphp

    @if ((! $hasCombinedRelationManagerTabsWithContent) || (! count($relationManagers)))
        {{ $form() }}
    @endif

    @if (count($relationManagers))
        <x-filament-panels::resources.relation-managers
            :active-locale="isset($activeLocale) ? $activeLocale : null"
            :active-manager="$this->activeRelationManager ?? ($hasCombinedRelationManagerTabsWithContent ? null : array_key_first($relationManagers))"
            :content-tab-label="$this->getContentTabLabel()"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        >
            @if ($hasCombinedRelationManagerTabsWithContent)
                <x-slot name="content">
                    {{ $form() }}
                </x-slot>
            @endif
        </x-filament-panels::resources.relation-managers>
    @endif

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
                        {{ Str::ucfirst(__('quote.please_fill_the_unit_price_and_delivery_in_days_for_all_items')) }}
                    </h3>
                </div>
            </div>
        </div>
    @endif

    <x-filament-panels::form.actions
        :actions="[$this->sendQuote, $this->cancelEditQuote]"
        :full-width="$this->hasFullWidthFormActions()"
    />
</x-filament-panels::page>

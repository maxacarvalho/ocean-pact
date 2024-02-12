@use('App\Utils\Str')
@use('App\Enums\QuotesPortal\QuoteStatusEnum')

<div class="fi-ta-header flex flex-col gap-3 p-4 sm:px-6 sm:flex-row sm:items-center">
    <div class="grid gap-y-1 w-full">
        <h3 class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
            {{ $supplierName }}
        </h3>

        <div class="flex justify-between">
            <p class="fi-ta-header-description text-sm text-gray-600 dark:text-gray-400">
                {{ Str::ucfirst(__('quote.quote_proposal', ['proposal' => $this->quote->proposal_number])) }}
            </p>

            <x-filament::badge color="{{ $statusColor }}">
                {{ $statusLabel }}
            </x-filament::badge>
        </div>
    </div>
</div>

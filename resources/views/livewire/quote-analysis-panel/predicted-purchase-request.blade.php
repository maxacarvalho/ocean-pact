<div class="grid grid-cols-4 gap-4">
    <div class="bg-white shadow-sm rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="p-4 sm:px-6">
            <div>
                <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    {{ \App\Utils\Str::title(__('quote_analysis_panel.quick_actions_panel_title')) }}
                </h3>

                <form wire:submit="update" class="w-full pt-4">

                    {{ $this->form }}

                    @error('allTogglesDisabledProperty') <hr class="mt-5"> <span class="text-xs text-red-700">{{ $message }}</span> @enderror

                    <div class="flex justify-center w-full pt-6">
                        <x-filament::button wire:click="update">
                            {{ \App\Utils\Str::ucfirst(__('quote_analysis_panel.quick_actions_panel_load_products')) }}
                        </x-filament::button>
                    </div>

                </form>

                <x-filament-actions::modals />

            </div>
        </div>
    </div>
    <div class="col-span-3">
        {{ $this->table }}
    </div>
</div>

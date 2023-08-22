<x-filament-panels::page.simple>
    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/login.actions.register.before') }}

            {{ $this->registerAction }}
        </x-slot>
    @endif

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    @if($this->token)
        <div class="text-center">
            <a
                class="text-primary-600 hover:text-primary-700"
                href="{{ route('filament.admin.supplier-registration', ['token' => $this->token]) }}"
            >{{ \App\Utils\Str::ucfirst(__('invitation.register_here')) }}</a>
        </div>
    @endif
</x-filament-panels::page.simple>

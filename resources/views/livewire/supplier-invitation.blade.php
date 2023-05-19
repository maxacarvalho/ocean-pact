<x-register-card action="submit">
    <div class="w-full flex justify-center">
        <x-filament::brand />
    </div>

    <div>
        <h2 class="font-bold tracking-tight text-center text-2xl">
            {{ \App\Utils\Str::ucfirst(__('invitation.create_supplier_user_account')) }}
        </h2>
    </div>

    {{ $this->form }}

    <x-filament::button type="submit" class="w-full" form="register">
        {{ \App\Utils\Str::title(__('invitation.create_user_account')) }}
    </x-filament::button>
</x-register-card>

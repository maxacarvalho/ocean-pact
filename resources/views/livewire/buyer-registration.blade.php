<x-register-card action="submit">
    <div class="w-full flex justify-center">
        <x-filament::brand />
    </div>

    <div>
        <h2 class="font-bold tracking-tight text-center text-2xl">
            {{ \App\Utils\Str::title(__('invitation.finish_registration')) }}
        </h2>
    </div>

    {{ $this->form }}

    <x-filament::button type="submit" class="w-full" form="register">
        {{ \App\Utils\Str::title(__('invitation.finish_registration')) }}
    </x-filament::button>
</x-register-card>

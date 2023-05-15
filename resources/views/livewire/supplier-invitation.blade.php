<x-register-card action="submit">
    <div class="w-full flex justify-center">
        <x-filament::brand />
    </div>

    <div>
        <h2 class="font-bold tracking-tight text-center text-2xl">
            {{ \App\Utils\Str::ucfirst(__('invitation.create_supplier_user_account')) }}
        </h2>
        <p class="mt-2 text-sm text-center">
            {{ __('invitation.or_if_you_already_have_an_account') }}
            <a class="text-primary-600" href="{{route('filament.auth.login')}}">
                {{ strtolower(__('invitation.click_here_to_login')) }}
            </a>
        </p>
    </div>

    {{ $this->form }}

    <x-filament::button type="submit" class="w-full" form="register">
        {{ \App\Utils\Str::title(__('invitation.create_user_account')) }}
    </x-filament::button>
</x-register-card>

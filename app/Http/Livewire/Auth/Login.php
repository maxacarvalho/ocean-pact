<?php

namespace App\Http\Livewire\Auth;

use JeffGreco13\FilamentBreezy\Http\Livewire\Auth\Login as BreezyLogin;

class Login extends BreezyLogin
{
    public ?string $token;

    public function boot(): void
    {
        parent::boot();

        $this->token = null;
    }

    public function mount(): void
    {
        parent::mount();

        if (request()->query('token')) {
            if (! request()->hasValidSignature()) {
                abort(401);
            }

            $this->token = request()->query('token');
        }
    }
}

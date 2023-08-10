<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLoginPage;

class Login extends BaseLoginPage
{
    public ?string $token = null;

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

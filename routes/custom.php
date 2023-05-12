<?php

use App\Http\Livewire\BuyerRegistrationLivewireComponent;
use Illuminate\Routing\Router;

Route::domain(config('filament.domain'))
    ->middleware(config('filament.middleware.base'))
    ->name(config('filament-breezy.route_group_prefix'))
    ->prefix(config('filament.path'))
    ->group(function (Router $router) {
        $router->get('buyer-registration/{token}', BuyerRegistrationLivewireComponent::class)
            ->name('buyer-registration');
    });

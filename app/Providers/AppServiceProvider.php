<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Model::shouldBeStrict();

        if ($this->app->environment('production')) {
            Filament::registerScripts([
                asset('js/clarity.js'),
            ]);
        }

        Filament::registerScripts([
            'https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js',
        ], true);

        Filament::serving(function () {
            Filament::registerViteTheme('resources/css/app.css');
        });
    }
}

<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
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
            FilamentAsset::register([
                Js::make('clarity.js', asset('js/clarity.js')),
            ]);
        }
    }
}

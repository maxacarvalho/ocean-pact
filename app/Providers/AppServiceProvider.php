<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
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

        FilamentAsset::register([
            Css::make('prism.css', asset('vendor/prism.css'))->loadedOnRequest(),
            Js::make('prism.js', asset('vendor/prism.js'))->loadedOnRequest(),
        ]);

        FilamentView::registerRenderHook(
            'panels::body.start',
            fn (): string => Blade::render('<x-filament-impersonate::banner/>'),
        );
    }
}

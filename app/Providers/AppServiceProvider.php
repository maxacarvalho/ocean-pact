<?php

namespace App\Providers;

use App\Livewire\Synth\MoneySynth;
use App\Models\User;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const string HOME = '/';

    public function register(): void
    {
    }

    public function boot(): void
    {
        Model::shouldBeStrict();

        Livewire::propertySynthesizer(MoneySynth::class);

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

        FilamentView::registerRenderHook(
            'panels::user-menu.before',
            fn (): string => Blade::render('<livewire:locale-switcher/>'),
        );

        $this->bootAuth();

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    public function bootAuth(): void
    {
        Gate::define('viewPulse', function (User $user) {
            return $user->isSuperAdmin();
        });
    }
}

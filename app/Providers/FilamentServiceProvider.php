<?php

namespace App\Providers;

use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class FilamentServiceProvider extends PluginServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-simple-highlight-field');
    }
}

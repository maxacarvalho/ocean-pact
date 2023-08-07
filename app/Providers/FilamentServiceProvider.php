<?php

namespace App\Providers;

use Spatie\LaravelPackageTools\Package;

class FilamentServiceProvider extends \Spatie\LaravelPackageTools\PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-simple-highlight-field');
    }
}

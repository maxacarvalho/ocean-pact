<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Company;
use App\Models\User;
use App\Policies\CompanyPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    protected $policies = [
        Company::class => CompanyPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(static function (User $user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });
    }
}

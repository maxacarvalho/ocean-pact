<?php

namespace App\Providers;

use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\Payload;
use App\Models\QuotesPortal\Budget;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Quote;
use App\Models\User;
use App\Policies\BudgetPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\IntegrationTypePolicy;
use App\Policies\PayloadPolicy;
use App\Policies\QuotePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    protected $policies = [
        Budget::class => BudgetPolicy::class,
        Company::class => CompanyPolicy::class,
        User::class => UserPolicy::class,
        IntegrationType::class => IntegrationTypePolicy::class,
        Payload::class => PayloadPolicy::class,
        Quote::class => QuotePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('viewPulse', function (User $user) {
            return $user->isSuperAdmin();
        });
    }
}

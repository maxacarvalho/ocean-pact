<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Budget;
use App\Models\Company;
use App\Models\IntegrationType;
use App\Models\Payload;
use App\Models\Quote;
use App\Models\User;
use App\Policies\BudgetPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\IntegrationTypePolicy;
use App\Policies\PayloadPolicy;
use App\Policies\QuotePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
    }
}

<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_company');
    }

    public function view(User $user, Company $company): bool
    {
        return $user->can('view_company');
    }

    public function create(User $user): bool
    {
        return $user->can('create_company');
    }

    public function update(User $user, Company $company): bool
    {
        return $user->can('update_company');
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->can('delete_company');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_company');
    }
}

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

    public function forceDelete(User $user, Company $company): bool
    {
        return $user->can('force_delete_company');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_company');
    }

    public function restore(User $user, Company $company): bool
    {
        return $user->can('restore_company');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_company');
    }

    public function replicate(User $user, Company $company): bool
    {
        return $user->can('replicate_company');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_company');
    }
}

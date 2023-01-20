<?php

namespace App\Policies;

use App\Models\IntegrationType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IntegrationTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_company');
    }

    public function view(User $user, IntegrationType $integrationType): bool
    {
        return $user->can('view_integration_type');
    }

    public function create(User $user): bool
    {
        return $user->can('create_company');
    }

    public function update(User $user, IntegrationType $integrationType): bool
    {
        return $user->can('update_integration_type');
    }

    public function delete(User $user, IntegrationType $integrationType): bool
    {
        return $user->can('delete_integration_type');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_company');
    }

    public function forceDelete(User $user, IntegrationType $integrationType): bool
    {
        return $user->can('force_delete_integration_type');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_company');
    }

    public function restore(User $user, IntegrationType $integrationType): bool
    {
        return $user->can('restore_integration_type');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_company');
    }

    public function replicate(User $user, IntegrationType $integrationType): bool
    {
        return $user->can('replicate_integration_type');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_company');
    }
}

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
}

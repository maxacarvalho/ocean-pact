<?php

namespace App\Policies;

use App\Models\IntegraHub\IntegrationType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IntegrationTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_integration::type') || $user->can('view_any_integration_type');
    }

    public function view(User $user, IntegrationType $integrationType): bool
    {
        return $user->can('view_integration::type') || $user->can('view_integration_type');
    }

    public function create(User $user): bool
    {
        return $user->can('create_integration::type') || $user->can('create_integration_type');
    }

    public function update(User $user, IntegrationType $integrationType): bool
    {
        return $user->can('update_integration::type') || $user->can('update_integration_type');
    }

    public function delete(User $user, IntegrationType $integrationType): bool
    {
        return $user->can('delete_integration::type') || $user->can('delete_integration_type');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_integration::type') || $user->can('delete_any_integration_type');
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response|bool
    {
        return $user->can('view_any_role');
    }

    public function view(User $user, Role $role): Response|bool
    {
        return $user->can('view_role');
    }

    public function create(User $user): Response|bool
    {
        return $user->can('create_role');
    }

    public function update(User $user, Role $role): Response|bool
    {
        return $user->can('update_role');
    }

    public function delete(User $user, Role $role): Response|bool
    {
        return $user->can('delete_role');
    }

    public function deleteAny(User $user): Response|bool
    {
        return $user->can('delete_any_role');
    }

    public function forceDelete(User $user, Role $role): Response|bool
    {
        return $user->can('{{ ForceDelete }}');
    }

    public function forceDeleteAny(User $user): Response|bool
    {
        return $user->can('{{ ForceDeleteAny }}');
    }

    public function restore(User $user, Role $role): Response|bool
    {
        return $user->can('{{ Restore }}');
    }

    public function restoreAny(User $user): Response|bool
    {
        return $user->can('{{ RestoreAny }}');
    }

    public function replicate(User $user, Role $role): Response|bool
    {
        return $user->can('{{ Replicate }}');
    }

    public function reorder(User $user): Response|bool
    {
        return $user->can('{{ Reorder }}');
    }
}

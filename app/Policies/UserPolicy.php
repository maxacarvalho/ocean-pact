<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response|bool
    {
        return $user->can('view_any_user');
    }

    public function view(User $user): Response|bool
    {
        return $user->can('view_user');
    }

    public function create(User $user): Response|bool
    {
        return $user->can('create_user');
    }

    public function update(User $user): Response|bool
    {
        return $user->can('update_user');
    }

    public function delete(User $user): Response|bool
    {
        return $user->can('delete_user');
    }

    public function deleteAny(User $user): Response|bool
    {
        return $user->can('delete_any_user');
    }

    public function forceDelete(User $user): Response|bool
    {
        return $user->can('force_delete_user');
    }

    public function forceDeleteAny(User $user): Response|bool
    {
        return $user->can('force_delete_any_user');
    }

    public function restore(User $user): Response|bool
    {
        return $user->can('restore_user');
    }

    public function restoreAny(User $user): Response|bool
    {
        return $user->can('restore_any_user');
    }

    public function replicate(User $user): Response|bool
    {
        return $user->can('replicate_user');
    }

    public function reorder(User $user): Response|bool
    {
        return $user->can('reorder_user');
    }
}

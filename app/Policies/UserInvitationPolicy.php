<?php

namespace App\Policies;

use App\Models\QuotesPortal\UserInvitation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserInvitationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_user_invitation') || $user->can('view_any_user::invitation');
    }

    public function view(User $user, UserInvitation $userInvitation): bool
    {
        return $user->can('view_user_invitation') || $user->can('view_user::invitation');
    }

    public function create(User $user): bool
    {
        return $user->can('create_user_invitation') || $user->can('create_user::invitation');
    }

    public function update(User $user, UserInvitation $userInvitation): bool
    {
        return $user->can('update_user_invitation') || $user->can('update_user::invitation');
    }

    public function delete(User $user, UserInvitation $userInvitation): bool
    {
        return $user->can('delete_user_invitation') || $user->can('delete_user::invitation');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_user_invitation') || $user->can('delete_any_user::invitation');
    }
}

<?php

namespace App\Policies;

use App\Models\QuotesPortal\BuyerInvitation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BuyerInvitationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_buyer_invitation') || $user->can('view_any_buyer::invitation');
    }

    public function view(User $user, BuyerInvitation $buyerInvitation): bool
    {
        return $user->can('view_buyer_invitation') || $user->can('view_buyer::invitation');
    }

    public function create(User $user): bool
    {
        return $user->can('create_buyer_invitation') || $user->can('create_buyer::invitation');
    }

    public function update(User $user, BuyerInvitation $buyerInvitation): bool
    {
        return $user->can('update_buyer_invitation') || $user->can('update_buyer::invitation');
    }

    public function delete(User $user, BuyerInvitation $buyerInvitation): bool
    {
        return $user->can('delete_buyer_invitation') || $user->can('delete_buyer::invitation');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_buyer_invitation') || $user->can('delete_any_buyer::invitation');
    }
}

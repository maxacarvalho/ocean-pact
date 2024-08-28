<?php

namespace App\Policies;

use App\Models\QuotesPortal\PurchaseRequestItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseRequestItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_purchase_request_item');
    }

    public function view(User $user, PurchaseRequestItem $purchaseRequestItem): bool
    {
        return $user->can('view_purchase_request_item');
    }

    public function create(User $user): bool
    {
        return $user->can('create_purchase_request_item');
    }

    public function update(User $user, PurchaseRequestItem $purchaseRequestItem): bool
    {
        return $user->can('update_purchase_request_item');
    }

    public function delete(User $user, PurchaseRequestItem $purchaseRequestItem): bool
    {
        return $user->can('delete_purchase_request_item');
    }

    public function deleteAny(User $user)
    {
        return $user->can('delete_any_purchase_request_item');
    }
}

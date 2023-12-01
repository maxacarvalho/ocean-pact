<?php

namespace App\Policies;

use App\Models\QuotesPortal\PurchaseRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_purchase::request') || $user->can('view_any_purchase_request');
    }

    public function view(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return $user->can('view_purchase::request') || $user->can('view_purchase_request');
    }

    public function create(User $user): bool
    {
        return $user->can('create_purchase::request') || $user->can('create_purchase_request');
    }

    public function update(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return $user->can('update_purchase::request') || $user->can('update_purchase_request');
    }

    public function delete(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return $user->can('delete_purchase::request') || $user->can('delete_purchase_request');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_purchase::request') || $user->can('delete_any_purchase_request');
    }
}

<?php

namespace App\Policies;

use App\Models\QuoteItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuoteItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_quote::item') || $user->can('view_any_quote_item');
    }

    public function view(User $user, QuoteItem $paymentCondition): bool
    {
        return $user->can('view_quote::item') || $user->can('view_quote_item');
    }

    public function create(User $user): bool
    {
        return $user->can('create_quote::item') || $user->can('create_quote_item');
    }

    public function update(User $user, QuoteItem $paymentCondition): bool
    {
        return $user->can('update_quote::item') || $user->can('update_quote_item');
    }

    public function delete(User $user, QuoteItem $paymentCondition): bool
    {
        return $user->can('delete_quote::item') || $user->can('delete_quote_item');
    }

    public function deleteAny(User $user, QuoteItem $paymentCondition): bool
    {
        return $user->can('delete_any_quote::item') || $user->can('delete_any_quote_item');
    }
}

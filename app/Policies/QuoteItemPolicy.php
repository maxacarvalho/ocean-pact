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
        return $user->can('view_any_quote::item') || $user->can('view_any_quote_item') || $user->can('view_any_quote');
    }

    public function view(User $user, QuoteItem $quoteItem): bool
    {
        return ($user->can('view_quote::item') || $user->can('view_quote_item') || $user->can('view_quote'))
            && ($quoteItem->quote->isResponded() || $quoteItem->quote->isAnalyzed());
    }

    public function create(User $user): bool
    {
        return $user->can('create_quote::item') || $user->can('create_quote_item') || $user->can('create_quote');
    }

    public function update(User $user, QuoteItem $quoteItem): bool
    {
        return
            ($user->can('update_quote::item') || $user->can('update_quote_item') || $user->can('update_quote'))
            && $quoteItem->quote->canBeResponded();
    }

    public function delete(User $user, QuoteItem $quoteItem): bool
    {
        return $user->can('delete_quote::item') || $user->can('delete_quote_item') || $user->can('delete_quote');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_quote::item') || $user->can('delete_any_quote_item') || $user->can('delete_any_quote');
    }
}

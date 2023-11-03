<?php

namespace App\Policies;

use App\Models\QuotesPortal\Quote;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuotePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_quote');
    }

    public function view(User $user, Quote $quote): bool
    {
        if ($user->isSeller()) {
            return $quote->supplier_id === $user->supplier_id
                && $user->can('view_quote') && ($quote->isResponded() || $quote->isAnalyzed());
        }

        return $user->can('view_quote') && ($quote->isResponded() || $quote->isAnalyzed());
    }

    public function create(User $user): bool
    {
        return $user->can('create_quote');
    }

    public function update(User $user, Quote $quote): bool
    {
        if ($user->isSeller()) {
            return $quote->supplier_id === $user->supplier_id
                && $user->can('update_quote') && $quote->canBeResponded();
        }

        return $user->can('update_quote') && $quote->canBeResponded();
    }

    public function delete(User $user, Quote $quote): bool
    {
        return $user->can('delete_quote');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_quote');
    }
}

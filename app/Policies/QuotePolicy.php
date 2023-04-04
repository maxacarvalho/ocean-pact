<?php

namespace App\Policies;

use App\Models\Quote;
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
        return $user->can('view_quote');
    }

    public function create(User $user): bool
    {
        return $user->can('create_quote');
    }

    public function update(User $user, Quote $quote): bool
    {
        return $user->can('update_quote');
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

<?php

namespace App\Policies;

use App\Models\QuotesPortal\Currency;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurrencyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_currency');
    }

    public function view(User $user, Currency $currency): bool
    {
        return $user->can('view_currency');
    }

    public function create(User $user): bool
    {
        return $user->can('create_currency');
    }

    public function update(User $user, Currency $currency): bool
    {
        return $user->can('update_currency');
    }

    public function delete(User $user, Currency $currency): bool
    {
        return $user->can('delete_currency');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_currency');
    }
}

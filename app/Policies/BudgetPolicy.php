<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BudgetPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_budget');
    }

    public function view(User $user, Budget $paymentCondition): bool
    {
        return $user->can('view_budget');
    }

    public function create(User $user): bool
    {
        return $user->can('create_budget');
    }

    public function update(User $user, Budget $paymentCondition): bool
    {
        return $user->can('update_budget');
    }

    public function delete(User $user, Budget $paymentCondition): bool
    {
        return $user->can('delete_budget');
    }

    public function deleteAny(User $user, Budget $paymentCondition): bool
    {
        return $user->can('delete_any_budget');
    }
}

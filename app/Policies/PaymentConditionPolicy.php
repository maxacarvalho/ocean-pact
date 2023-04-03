<?php

namespace App\Policies;

use App\Models\PaymentCondition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentConditionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_payment_condition') || $user->can('view_any_payment::condition');
    }

    public function view(User $user, PaymentCondition $paymentCondition): bool
    {
        return $user->can('view_payment_condition') || $user->can('view_payment::condition');
    }

    public function create(User $user): bool
    {
        return $user->can('create_payment_condition') || $user->can('create_payment::condition');
    }

    public function update(User $user, PaymentCondition $paymentCondition): bool
    {
        return $user->can('update_payment_condition') || $user->can('update_payment::condition');
    }

    public function delete(User $user, PaymentCondition $paymentCondition): bool
    {
        return $user->can('delete_payment_condition') || $user->can('delete_payment::condition');
    }

    public function deleteAny(User $user, PaymentCondition $paymentCondition): bool
    {
        return $user->can('delete_any_payment_condition') || $user->can('delete_any_payment::condition');
    }
}

<?php

namespace App\Policies;

use App\Models\QuotesPortal\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_supplier');
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $user->can('view_supplier');
    }

    public function create(User $user): bool
    {
        return $user->can('create_supplier');
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->can('update_supplier');
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->can('delete_supplier');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_supplier');
    }
}

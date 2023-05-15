<?php

namespace App\Policies;

use App\Models\SupplierInvitation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierInvitationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_supplier_invitation') || $user->can('view_any_supplier::invitation');
    }

    public function view(User $user, SupplierInvitation $supplierInvitation): bool
    {
        return $user->can('view_supplier_invitation') || $user->can('view_supplier::invitation');
    }

    public function create(User $user): bool
    {
        return $user->can('create_supplier_invitation') || $user->can('create_supplier::invitation');
    }

    public function update(User $user, SupplierInvitation $supplierInvitation): bool
    {
        return $user->can('update_supplier_invitation') || $user->can('update_supplier::invitation');
    }

    public function delete(User $user, SupplierInvitation $supplierInvitation): bool
    {
        return $user->can('delete_supplier_invitation') || $user->can('delete_supplier::invitation');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_supplier_invitation') || $user->can('delete_any_supplier::invitation');
    }
}

<?php

namespace App\Policies;

use App\Models\Payload;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayloadPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_payload');
    }

    public function view(User $user, Payload $payload): bool
    {
        return $user->can('view_payload');
    }

    public function create(User $user): bool
    {
        return $user->can('create_payload');
    }

    public function update(User $user, Payload $payload): bool
    {
        return $user->can('update_payload');
    }

    public function delete(User $user, Payload $payload): bool
    {
        return $user->can('delete_payload');
    }
}

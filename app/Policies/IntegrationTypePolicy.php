<?php

namespace App\Policies;

use App\Models\IntegrationType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IntegrationTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
    }

    public function view(User $user, IntegrationType $integrationType): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, IntegrationType $integrationType): bool
    {
    }

    public function delete(User $user, IntegrationType $integrationType): bool
    {
    }

    public function restore(User $user, IntegrationType $integrationType): bool
    {
    }

    public function forceDelete(User $user, IntegrationType $integrationType): bool
    {
    }
}

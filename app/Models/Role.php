<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role as SpatieRoleModel;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $guard_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Role extends SpatieRoleModel
{
    public const TABLE_NAME = 'roles';
    public const ID = 'id';
    public const NAME = 'name';
    public const GUARD_NAME = 'guard_name';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'Admin';
    public const ROLE_USER = 'user';
    public const ROLE_BUYER = 'buyer';
    public const ROLE_SELLER = 'seller';
}

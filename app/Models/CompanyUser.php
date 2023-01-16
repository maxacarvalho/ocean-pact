<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyUser extends Pivot
{
    public const TABLE_NAME = 'company_user';
    public const COMPANY_ID = 'company_id';
    public const USER_ID = 'user_id';
}

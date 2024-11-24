<?php

namespace App\Models\QuotesPortal;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $company_id
 * @property int $user_id
 * @property string $buyer_code
 */
class CompanyUser extends Pivot
{
    public const TABLE_NAME = 'company_user';

    public const COMPANY_ID = 'company_id';

    public const USER_ID = 'user_id';

    public const BUYER_CODE = 'buyer_code';

    protected $table = self::TABLE_NAME;

    public $timestamps = false;
}

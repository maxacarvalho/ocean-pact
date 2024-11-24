<?php

namespace App\Models\QuotesPortal;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $supplier_id
 * @property int $user_id
 * @property string $code
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SupplierUser extends Pivot
{
    public const TABLE_NAME = 'supplier_user';

    public const SUPPLIER_ID = 'supplier_id';

    public const USER_ID = 'user_id';

    public const CODE = 'code';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    public $timestamps = true;
}

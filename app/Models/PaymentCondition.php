<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int               $id
 * @property int|null          $company_id
 * @property string            $code
 * @property string            $description
 * @property Carbon|null       $created_at
 * @property Carbon|null       $updated_at
 * @property-read Company|null $company
 */
class PaymentCondition extends Model
{
    public const TABLE_NAME = 'payment_conditions';
    public const ID = 'id';
    public const COMPANY_ID = 'company_id';
    public const CODE = 'code';
    public const DESCRIPTION = 'description';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_COMPANY = 'company';

    protected $table = self::TABLE_NAME;
    protected $guarded = [
        self::ID,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            self::COMPANY_ID,
            Company::CODE_BRANCH
        );
    }
}

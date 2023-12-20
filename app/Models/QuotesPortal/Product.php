<?php

namespace App\Models\QuotesPortal;

use App\Casts\QuotesPortal\MoneyFromJsonCast;
use App\Utils\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int               $id
 * @property string            $company_code
 * @property string|null       $company_code_branch
 * @property string            $code
 * @property string            $description
 * @property string            $measurement_unit
 * @property Money             $last_price
 * @property Money             $smallest_price
 * @property int               $smallest_eta
 * @property Carbon|null       $created_at
 * @property Carbon|null       $updated_at
 * Relations
 * @property-read Company|null $company
 */
class Product extends Model
{
    public const TABLE_NAME = 'products';
    public const ID = 'id';
    public const COMPANY_CODE = 'company_code';
    public const COMPANY_CODE_BRANCH = 'company_code_branch';
    public const CODE = 'code';
    public const DESCRIPTION = 'description';
    public const MEASUREMENT_UNIT = 'measurement_unit';
    public const LAST_PRICE = 'last_price';
    public const SMALLEST_PRICE = 'smallest_price';
    public const SMALLEST_ETA = 'smallest_eta';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_COMPANY = 'company';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::LAST_PRICE => MoneyFromJsonCast::class,
        self::SMALLEST_PRICE => MoneyFromJsonCast::class,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            self::COMPANY_CODE,
            Company::CODE
        );
    }
}

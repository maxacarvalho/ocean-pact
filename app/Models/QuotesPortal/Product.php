<?php

namespace App\Models\QuotesPortal;

use App\Casts\QuotesPortal\MoneyFromJsonCast;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use NumberFormatter;

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

    protected $attributes = [
        self::LAST_PRICE => [
            'currency' => 'BRL',
            'amount' => 0,
        ],
        self::SMALLEST_PRICE => [
            'currency' => 'BRL',
            'amount' => 0,
        ],
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            self::COMPANY_CODE,
            Company::CODE
        );
    }

    public function getFormattedPrice(Money $money): string
    {
        $currency = $money->getCurrency()->getCurrencyCode();

        if ('BRL' === $currency) {
            $formatter = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
            $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, ',');
            $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '.');
        } else {
            $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);
            $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, '.');
            $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, ',');
        }

        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        return $money->formatWith($formatter);
    }
}

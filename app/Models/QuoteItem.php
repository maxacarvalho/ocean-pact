<?php

namespace App\Models;

use App\Enums\FreightTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int                  $id
 * @property int                  $quote_id
 * @property int                  $product_id
 * @property string               $description
 * @property string               $measurement_unit
 * @property string               $item
 * @property int                  $quantity
 * @property int                  $unit_price
 * @property int                  $ipi
 * @property int                  $icms
 * @property FreightTypeEnum|null $freight_type
 * @property int                  $freight_cost
 * @property int                  $expenses
 * @property Carbon|null          $delivery_date
 * @property bool                 $should_be_quoted
 * @property string|null          $comments
 * @property Carbon|null          $created_at
 * @property Carbon|null          $updated_at
 * @property-read Quote           $quote
 * @property-read Product         $product
 */
class QuoteItem extends Model
{
    public const TABLE_NAME = 'quote_items';
    public const ID = 'id';
    public const QUOTE_ID = 'quote_id';
    public const PRODUCT_ID = 'product_id';
    public const DESCRIPTION = 'description';
    public const MEASUREMENT_UNIT = 'measurement_unit';
    public const ITEM = 'item';
    public const QUANTITY = 'quantity';
    public const UNIT_PRICE = 'unit_price';
    public const IPI = 'ipi';
    public const ICMS = 'icms';
    public const FREIGHT_TYPE = 'freight_type';
    public const FREIGHT_COST = 'freight_cost';
    public const EXPENSES = 'expenses';
    public const DELIVERY_DATE = 'delivery_date';
    public const SHOULD_BE_QUOTED = 'should_be_quoted';
    public const COMMENTS = 'comments';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_QUOTE = 'quote';
    public const RELATION_PRODUCT = 'product';

    protected $table = self::TABLE_NAME;
    protected $guarded = [
        self::ID,
    ];
    protected $casts = [
        self::FREIGHT_TYPE => FreightTypeEnum::class.':nullable',
        self::DELIVERY_DATE => 'date',
        self::SHOULD_BE_QUOTED => 'boolean',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\QuoteItemStatusEnum;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int                 $id
 * @property int                 $quote_id
 * @property int                 $product_id
 * @property string              $description
 * @property string              $measurement_unit
 * @property string              $item
 * @property int                 $quantity
 * @property Money               $unit_price
 * @property string              $currency
 * @property int                 $ipi
 * @property int                 $icms
 * @property int                 $delivery_in_days
 * @property bool                $should_be_quoted
 * @property QuoteItemStatusEnum $status
 * @property string|null         $comments
 * @property string|null         $seller_image
 * @property string|null         $buyer_image
 * @property Carbon|null         $created_at
 * @property Carbon|null         $updated_at
 * // Relations
 * @property-read Quote          $quote
 * @property-read Product        $product
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
    public const CURRENCY = 'currency';
    public const IPI = 'ipi';
    public const ICMS = 'icms';
    public const DELIVERY_IN_DAYS = 'delivery_in_days';
    public const SHOULD_BE_QUOTED = 'should_be_quoted';
    public const STATUS = 'status';
    public const COMMENTS = 'comments';
    public const SELLER_IMAGE = 'seller_image';
    public const BUYER_IMAGE = 'buyer_image';
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
        self::UNIT_PRICE => MoneyCast::class,
        self::SHOULD_BE_QUOTED => 'boolean',
        self::STATUS => QuoteItemStatusEnum::class,
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function canBeResponded(): bool
    {
        return in_array($this->status, [
            QuoteItemStatusEnum::PENDING,
            QuoteItemStatusEnum::RESPONDED,
        ]);
    }

    public function cannotBeResponded(): bool
    {
        return ! $this->canBeResponded();
    }
}

<?php

namespace App\Models\QuotesPortal;

use App\Casts\QuotesPortal\MoneyCast;
use App\Enums\QuotesPortal\QuoteItemStatusEnum;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use NumberFormatter;

/**
 * @property int $id
 * @property int $quote_id
 * @property int $product_id
 * @property string $description
 * @property string $measurement_unit
 * @property string $item
 * @property float $quantity
 * @property Money $unit_price
 * @property string $currency
 * @property float $ipi
 * @property float $icms
 * @property int $delivery_in_days
 * @property bool $should_be_quoted
 * @property QuoteItemStatusEnum $status
 * @property string|null $comments
 * @property string|null $seller_image
 * @property string|null $buyer_image
 * @property int|null $purchase_request_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *                                   Relations
 * @property-read Quote                $quote
 * @property-read Product              $product
 * @property-read PurchaseRequest|null $purchaseRequest
 */
class QuoteItem extends Model
{
    public const string TABLE_NAME = 'quote_items';

    public const string ID = 'id';

    public const string QUOTE_ID = 'quote_id';

    public const string PRODUCT_ID = 'product_id';

    public const string DESCRIPTION = 'description';

    public const string MEASUREMENT_UNIT = 'measurement_unit';

    public const string ITEM = 'item';

    public const string QUANTITY = 'quantity';

    public const string UNIT_PRICE = 'unit_price';

    public const string CURRENCY = 'currency';

    public const string IPI = 'ipi';

    public const string ICMS = 'icms';

    public const string DELIVERY_IN_DAYS = 'delivery_in_days';

    public const string SHOULD_BE_QUOTED = 'should_be_quoted';

    public const string STATUS = 'status';

    public const string COMMENTS = 'comments';

    public const string SELLER_IMAGE = 'seller_image';

    public const string BUYER_IMAGE = 'buyer_image';

    public const string PURCHASE_REQUEST_ID = 'purchase_request_id';

    public const string CREATED_AT = 'created_at';

    public const string UPDATED_AT = 'updated_at';

    // Relations
    public const string RELATION_QUOTE = 'quote';

    public const string RELATION_PRODUCT = 'product';

    public const string RELATION_PURCHASE_REQUEST = 'purchaseRequest';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected function casts(): array
    {
        return [
            self::UNIT_PRICE => MoneyCast::class,
            self::IPI => 'float',
            self::ICMS => 'float',
            self::SHOULD_BE_QUOTED => 'boolean',
            self::STATUS => QuoteItemStatusEnum::class,
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
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

    public function getFormattedUnitPrice(): string
    {
        $currency = $this->currency;

        if ($currency === 'BRL') {
            $formatter = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
            $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, ',');
            $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '.');
        } else {
            $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);
            $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, '.');
            $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, ',');
        }

        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        return $this->unit_price->formatWith($formatter);
    }
}

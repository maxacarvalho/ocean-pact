<?php

namespace App\Models\QuotesPortal;

use App\Casts\QuotesPortal\MoneyFromJsonCast;
use App\Models\User;
use App\Utils\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int            $id
 * @property int            $company_id
 * @property string         $quote_number
 * @property int            $buyer_id
 * @property int            $quote_id
 * @property int            $supplier_id
 * @property int            $product_id
 * @property string         $item
 * @property int            $quote_item_id
 * @property Carbon         $delivery_date
 * @property Money          $price
 * @property Money          $last_price
 * @property Carbon         $necessity_date
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 *
 * Relations
 * @property-read Company   $company
 * @property-read User      $buyer
 * @property-read Quote     $quote
 * @property-read Supplier  $supplier
 * @property-read Product   $product
 * @property-read QuoteItem $quoteItem
 */
class PredictedPurchaseRequest extends Model
{
    public const TABLE_NAME = 'predicted_purchase_requests';
    public const ID = 'id';
    public const COMPANY_ID = 'company_id';
    public const QUOTE_NUMBER = 'quote_number';
    public const BUYER_ID = 'buyer_id';
    public const QUOTE_ID = 'quote_id';
    public const SUPPLIER_ID = 'supplier_id';
    public const PRODUCT_ID = 'product_id';
    public const ITEM = 'item';
    public const QUOTE_ITEM_ID = 'quote_item_id';
    public const DELIVERY_DATE = 'delivery_date';
    public const PRICE = 'price';
    public const LAST_PRICE = 'last_price';
    public const NECESSITY_DATE = 'necessity_date';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_COMPANY = 'company';
    public const RELATION_BUYER = 'buyer';
    public const RELATION_QUOTE = 'quote';
    public const RELATION_SUPPLIER = 'supplier';
    public const RELATION_PRODUCT = 'product';
    public const RELATION_QUOTE_ITEM = 'quoteItem';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::DELIVERY_DATE => 'date',
        self::NECESSITY_DATE => 'date',
        self::PRICE => MoneyFromJsonCast::class,
        self::LAST_PRICE => MoneyFromJsonCast::class,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::BUYER_ID,
            User::ID
        );
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function quoteItem(): BelongsTo
    {
        return $this->belongsTo(QuoteItem::class);
    }
}

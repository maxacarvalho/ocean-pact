<?php

namespace App\Models\QuotesPortal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int                  $id
 * @property int                  $purchase_request_id
 * @property int                  $quote_item_id
 * @property string               $item
 * @property Carbon|null          $created_at
 * @property Carbon|null          $updated_at
 * Relations
 * @property-read PurchaseRequest $purchaseRequest
 */
class PurchaseRequestItem extends Model
{
    public const string TABLE_NAME = 'purchase_request_items';
    public const string ID = 'id';
    public const string PURCHASE_REQUEST_ID = 'purchase_request_id';
    public const string QUOTE_ITEM_ID = 'quote_item_id';
    public const string ITEM = 'item';
    public const string CREATED_AT = 'created_at';
    public const string UPDATED_AT = 'updated_at';

    // Relations
    public const string RELATION_PURCHASE_REQUEST = 'purchaseRequest';
    public const string RELATION_QUOTE_ITEM = 'quoteItem';

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function quoteItem(): BelongsTo
    {
        return $this->belongsTo(QuoteItem::class);
    }
}

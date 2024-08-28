<?php

namespace App\Models\QuotesPortal;

use App\Enums\QuotesPortal\PurchaseRequestStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int                                   $id
 * @property int                                   $quote_id
 * @property string                                $purchase_request_number
 * @property Carbon|null                           $sent_at
 * @property Carbon|null                           $viewed_at
 * @property PurchaseRequestStatus                 $status
 * @property string|null                           $file
 * @property Carbon|null                           $created_at
 * @property Carbon|null                           $updated_at
 * Relations
 * @property-read  Quote                           $quote
 * @property-read PurchaseRequestItem[]|Collection $items
 */
class PurchaseRequest extends Model
{
    public const string TABLE_NAME = 'purchase_requests';
    public const string ID = 'id';
    public const string QUOTE_ID = 'quote_id';
    public const string PURCHASE_REQUEST_NUMBER = 'purchase_request_number';
    public const string SENT_AT = 'sent_at';
    public const string VIEWED_AT = 'viewed_at';
    public const string STATUS = 'status';
    public const string FILE = 'file';
    public const string CREATED_AT = 'created_at';
    public const string UPDATED_AT = 'updated_at';

    // Relations
    public const string RELATION_QUOTE = 'quote';
    public const string RELATION_ITEMS = 'items';

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected function casts(): array
    {
        return [
            self::SENT_AT => 'datetime',
            self::VIEWED_AT => 'datetime',
            self::STATUS => PurchaseRequestStatus::class,
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }
}

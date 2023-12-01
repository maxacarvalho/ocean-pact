<?php

namespace App\Models\QuotesPortal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $quote_id
 * @property string      $purchase_request_number
 * @property Carbon|null $sent_at
 * @property Carbon|null $viewed_at
 * @property string|null $file
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * Relations
 * @property Quote       $quote
 */
class PurchaseRequest extends Model
{
    public const TABLE_NAME = 'purchase_requests';
    public const ID = 'id';
    public const QUOTE_ID = 'quote_id';
    public const PURCHASE_REQUEST_NUMBER = 'purchase_request_number';
    public const SENT_AT = 'sent_at';
    public const VIEWED_AT = 'viewed_at';
    public const FILE = 'file';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_QUOTE = 'quote';

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::SENT_AT => 'datetime',
        self::VIEWED_AT => 'datetime',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}

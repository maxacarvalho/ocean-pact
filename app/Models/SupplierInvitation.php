<?php

namespace App\Models;

use App\Enums\InvitationStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int                  $id
 * @property int                  $supplier_id
 * @property int                  $quote_id
 * @property string|null          $token
 * @property Carbon|null          $sent_at
 * @property InvitationStatusEnum $status
 * @property Carbon|null          $created_at
 * @property Carbon|null          $updated_at
 * @property-read Supplier        $supplier
 * @property-read Quote           $quote
 */
class SupplierInvitation extends Model
{
    public const TABLE_NAME = 'supplier_invitations';
    public const ID = 'id';
    public const SUPPLIER_ID = 'supplier_id';
    public const QUOTE_ID = 'quote_id';
    public const TOKEN = 'token';
    public const SENT_AT = 'sent_at';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_SUPPLIER = 'supplier';
    public const RELATION_QUOTE = 'quote';

    protected $table = self::TABLE_NAME;
    protected $guarded = [
        self::ID,
    ];
    protected $casts = [
        self::SENT_AT => 'datetime',
        self::STATUS => InvitationStatusEnum::class,
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(
            Supplier::class,
            self::SUPPLIER_ID,
            Supplier::ID
        );
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(
            Quote::class,
            self::QUOTE_ID,
            Quote::ID
        );
    }
}

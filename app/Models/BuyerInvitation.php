<?php

namespace App\Models;

use App\Enums\InvitationStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int                  $id
 * @property int                  $buyer_id
 * @property string|null          $token
 * @property Carbon|null          $registered_at
 * @property Carbon|null          $sent_at
 * @property InvitationStatusEnum $status
 * @property Carbon|null          $created_at
 * @property Carbon|null          $updated_at
 * @property-read User            $buyer
 */
class BuyerInvitation extends Model
{
    public const TABLE_NAME = 'buyer_invitations';
    public const ID = 'id';
    public const BUYER_ID = 'buyer_id';
    public const TOKEN = 'token';
    public const REGISTERED_AT = 'registered_at';
    public const SENT_AT = 'sent_at';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_BUYER = 'buyer';

    protected $table = self::TABLE_NAME;
    protected $guarded = [
        self::ID,
    ];
    protected $casts = [
        self::REGISTERED_AT => 'datetime',
        self::SENT_AT => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::BUYER_ID,
            User::ID
        );
    }
}

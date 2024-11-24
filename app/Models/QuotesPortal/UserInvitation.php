<?php

namespace App\Models\QuotesPortal;

use App\Enums\QuotesPortal\InvitationStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $token
 * @property Carbon|null $registered_at
 * @property Carbon|null $sent_at
 * @property InvitationStatusEnum $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $quote_id
 *                              Relations
 * @property-read User            $user
 * @property-read Quote|null      $quote
 */
class UserInvitation extends Model
{
    public const TABLE_NAME = 'user_invitations';

    public const ID = 'id';

    public const USER_ID = 'user_id';

    public const TOKEN = 'token';

    public const REGISTERED_AT = 'registered_at';

    public const SENT_AT = 'sent_at';

    public const STATUS = 'status';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const QUOTE_ID = 'quote_id';

    // Relations
    public const RELATION_USER = 'user';

    public const RELATION_QUOTE = 'quote';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected function casts(): array
    {
        return [
            self::REGISTERED_AT => 'datetime',
            self::SENT_AT => 'datetime',
            self::STATUS => InvitationStatusEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::USER_ID,
            User::ID
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

    public function markAsSent(): void
    {
        $this->update([
            UserInvitation::SENT_AT => now(),
            UserInvitation::STATUS => InvitationStatusEnum::SENT,
        ]);
    }

    public function markAsAccepted(): void
    {
        $this->update([
            UserInvitation::STATUS => InvitationStatusEnum::ACCEPTED,
            UserInvitation::REGISTERED_AT => now(),
        ]);
    }
}

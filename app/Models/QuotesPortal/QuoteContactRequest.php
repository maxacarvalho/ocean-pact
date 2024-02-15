<?php

namespace App\Models\QuotesPortal;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int        $id
 * @property int        $quote_id
 * @property int        $user_id
 * @property string     $body
 * @property array      $recipients
 * @property string     $created_at
 * @property string     $updated_at
 * @property-read Quote $quote
 * @property-read User  $user
 */
class QuoteContactRequest extends Model
{
    public const string TABLE_NAME = 'quote_contact_requests';
    public const string ID = 'id';
    public const string QUOTE_ID = 'quote_id';
    public const string USER_ID = 'user_id';
    public const string BODY = 'body';
    public const string RECIPIENTS = 'recipients';
    public const string CREATED_AT = 'created_at';
    public const string UPDATED_AT = 'updated_at';

    // Relations
    public const string RELATION_QUOTE = 'quote';
    public const string RELATION_USER = 'user';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::RECIPIENTS => 'array',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

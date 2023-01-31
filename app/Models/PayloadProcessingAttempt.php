<?php

namespace App\Models;

use App\Enums\PayloadProcessingAttemptsStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int                                 $id
 * @property int                                 $payload_id
 * @property PayloadProcessingAttemptsStatusEnum $status
 * @property string|null                         $message
 * @property Carbon|null                         $created_at
 * @property Carbon|null                         $updated_at
 * @property-read Payload                        $payload
 */
class PayloadProcessingAttempt extends Model
{
    public const TABLE_NAME = 'payload_processing_attempts';
    public const ID = 'id';
    public const PAYLOAD_ID = 'payload_id';
    public const STATUS = 'status';
    public const MESSAGE = 'message';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
    ];

    protected $casts = [
        self::STATUS => PayloadProcessingAttemptsStatusEnum::class,
    ];

    public function payload(): BelongsTo
    {
        return $this->belongsTo(Payload::class);
    }
}

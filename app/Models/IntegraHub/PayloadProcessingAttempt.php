<?php

namespace App\Models\IntegraHub;

use App\Enums\IntegraHub\PayloadProcessingAttemptsStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int                                 $id
 * @property int                                 $payload_id
 * @property PayloadProcessingAttemptsStatusEnum $status
 * @property string|null                         $message
 * @property array|null                          $response
 * @property Carbon|null                         $created_at
 * @property Carbon|null                         $updated_at
 * Relations
 * @property-read Payload                        $payload
 */
class PayloadProcessingAttempt extends Model
{
    public const TABLE_NAME = 'payload_processing_attempts';
    public const ID = 'id';
    public const PAYLOAD_ID = 'payload_id';
    public const STATUS = 'status';
    public const MESSAGE = 'message';
    public const RESPONSE = 'response';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_PAYLOAD = 'payload';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected function casts(): array
    {
        return [
            self::STATUS => PayloadProcessingAttemptsStatusEnum::class,
            self::RESPONSE => 'array',
        ];
    }

    public function payload(): BelongsTo
    {
        return $this->belongsTo(Payload::class);
    }
}

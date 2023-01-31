<?php

namespace App\Models;

use App\Enums\PayloadProcessingStatusEnum;
use App\Enums\PayloadStoringStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                              $id
 * @property int                              $integration_type_id
 * @property string                           $payload
 * @property string|null                      $stored_at
 * @property PayloadStoringStatusEnum         $storing_status
 * @property string|null                      $processed_at
 * @property PayloadProcessingStatusEnum|null $processing_status
 * @property string                           $created_at
 * @property string                           $updated_at
 * @property-read IntegrationType             $integrationType
 * @property-read PayloadProcessingAttempt    $processingAttempts
 */
class Payload extends Model
{
    public const TABLE_NAME = 'payloads';
    public const ID = 'id';
    public const INTEGRATION_TYPE_ID = 'integration_type_id';
    public const PAYLOAD = 'payload';
    public const STORED_AT = 'stored_at';
    public const STORING_STATUS = 'storing_status';
    public const PROCESSED_AT = 'processed_at';
    public const PROCESSING_STATUS = 'processing_status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
    ];

    public static array $columns = [
        self::ID,
        self::INTEGRATION_TYPE_ID,
        self::PAYLOAD,
        self::STORED_AT,
        self::STORING_STATUS,
        self::PROCESSED_AT,
        self::PROCESSING_STATUS,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::PAYLOAD => 'array',
        self::STORED_AT => 'datetime',
        self::STORING_STATUS => PayloadStoringStatusEnum::class,
        self::PROCESSED_AT => 'datetime',
        self::PROCESSING_STATUS => PayloadProcessingStatusEnum::class.':nullable',
    ];

    public function integrationType(): BelongsTo
    {
        return $this->belongsTo(IntegrationType::class);
    }

    public function processingAttempts(): HasMany
    {
        return $this->hasMany(PayloadProcessingAttempt::class);
    }
}

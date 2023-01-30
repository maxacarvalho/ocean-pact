<?php

namespace App\Models;

use App\Enums\PayloadProcessedStatusEnum;
use App\Enums\PayloadStoredStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                        $id
 * @property int                        $integration_type_id
 * @property string                     $payload
 * @property string|null                $stored_at
 * @property PayloadStoredStatusEnum    $stored_status
 * @property string|null                $processed_at
 * @property PayloadProcessedStatusEnum $processed_status
 * @property string                     $created_at
 * @property string                     $updated_at
 * @property-read IntegrationType       $integrationType
 */
class Payload extends Model
{
    public const TABLE_NAME = 'payloads';
    public const ID = 'id';
    public const INTEGRATION_TYPE_ID = 'integration_type_id';
    public const PAYLOAD = 'payload';
    public const STORED_AT = 'stored_at';
    public const STORED_STATUS = 'stored_status';
    public const PROCESSED_AT = 'processed_at';
    public const PROCESSED_STATUS = 'processed_status';
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
        self::STORED_STATUS,
        self::PROCESSED_AT,
        self::PROCESSED_STATUS,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::PAYLOAD => 'array',
        self::STORED_AT => 'datetime',
        self::STORED_STATUS => PayloadStoredStatusEnum::class,
        self::PROCESSED_AT => 'datetime',
        self::PROCESSED_STATUS => PayloadProcessedStatusEnum::class,
    ];

    public function integrationType(): BelongsTo
    {
        return $this->belongsTo(IntegrationType::class);
    }
}

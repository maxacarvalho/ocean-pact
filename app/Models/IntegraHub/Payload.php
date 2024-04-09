<?php

namespace App\Models\IntegraHub;

use App\Enums\IntegraHub\PayloadProcessingStatusEnum;
use App\Enums\IntegraHub\PayloadStoringStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                              $id
 * @property int                              $integration_type_id
 * @property array                            $original_payload
 * @property array                            $payload
 * @property array|null                       $path_parameters
 * @property string|null                      $payload_hash
 * @property Carbon|null                      $stored_at
 * @property PayloadStoringStatusEnum         $storing_status
 * @property Carbon|null                      $processed_at
 * @property PayloadProcessingStatusEnum|null $processing_status
 * @property array|null                       $response
 * @property string|null                      $error
 * @property Carbon|null                      $created_at
 * @property Carbon|null                      $updated_at
 * Relations
 * @property-read IntegrationType             $integrationType
 * @property-read PayloadProcessingAttempt    $processingAttempts
 */
class Payload extends Model
{
    public const TABLE_NAME = 'payloads';
    public const ID = 'id';
    public const INTEGRATION_TYPE_ID = 'integration_type_id';
    public const ORIGINAL_PAYLOAD = 'original_payload';
    public const PAYLOAD = 'payload';
    public const PATH_PARAMETERS = 'path_parameters';
    public const PAYLOAD_HASH = 'payload_hash';
    public const STORED_AT = 'stored_at';
    public const STORING_STATUS = 'storing_status';
    public const PROCESSED_AT = 'processed_at';
    public const PROCESSING_STATUS = 'processing_status';
    public const RESPONSE = 'response';
    public const ERROR = 'error';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_INTEGRATION_TYPE = 'integrationType';
    public const RELATION_PROCESSING_ATTEMPTS = 'processingAttempts';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected function casts(): array
    {
        return [
            self::PAYLOAD => 'array',
            self::PATH_PARAMETERS => 'array',
            self::STORED_AT => 'datetime',
            self::STORING_STATUS => PayloadStoringStatusEnum::class,
            self::PROCESSED_AT => 'datetime',
            self::PROCESSING_STATUS => PayloadProcessingStatusEnum::class,
            self::RESPONSE => 'array',
            self::ORIGINAL_PAYLOAD => 'array',
        ];
    }

    public function integrationType(): BelongsTo
    {
        return $this->belongsTo(IntegrationType::class);
    }

    public function processingAttempts(): HasMany
    {
        return $this->hasMany(PayloadProcessingAttempt::class);
    }

    public function isReady(): bool
    {
        return $this->processing_status === PayloadProcessingStatusEnum::READY;
    }

    public function isProcessing(): bool
    {
        return $this->processing_status === PayloadProcessingStatusEnum::PROCESSING;
    }

    public function isCollected(): bool
    {
        return $this->processing_status === PayloadProcessingStatusEnum::COLLECTED;
    }

    public function isFailed(): bool
    {
        return $this->processing_status === PayloadProcessingStatusEnum::FAILED;
    }

    public function markAsProcessing(): void
    {
        $this->update([
            self::PROCESSING_STATUS => PayloadProcessingStatusEnum::PROCESSING,
        ]);
    }

    public function markAsDone(?array $response): void
    {
        $this->update([
            self::PROCESSING_STATUS => PayloadProcessingStatusEnum::DONE,
            self::PROCESSED_AT => now(),
            self::RESPONSE => $response,
        ]);
    }

    public function markAsFailed(string $error, ?array $response): void
    {
        $this->update([
            self::PROCESSING_STATUS => PayloadProcessingStatusEnum::FAILED,
            self::ERROR => $error,
            self::RESPONSE => $response,
        ]);
    }
}

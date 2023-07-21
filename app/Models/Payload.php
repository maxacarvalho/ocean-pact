<?php

namespace App\Models;

use App\Enums\PayloadProcessingStatusEnum;
use App\Enums\PayloadStoringStatusEnum;
use App\Jobs\PayloadProcessors\PayloadProcessor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

/**
 * @property int                              $id
 * @property int                              $integration_type_id
 * @property array                            $payload
 * @property array|null                       $payload_hash
 * @property string|null                      $stored_at
 * @property PayloadStoringStatusEnum         $storing_status
 * @property string|null                      $processed_at
 * @property PayloadProcessingStatusEnum|null $processing_status
 * @property string|null                      $error
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
    public const PAYLOAD_HASH = 'payload_hash';
    public const STORED_AT = 'stored_at';
    public const STORING_STATUS = 'storing_status';
    public const PROCESSED_AT = 'processed_at';
    public const PROCESSING_STATUS = 'processing_status';
    public const ERROR = 'error';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_INTEGRATION_TYPE = 'integrationType';
    public const RELATION_PROCESSING_ATTEMPTS = 'processingAttempts';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
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

    public function isReady(): bool
    {
        return $this->processing_status->equals(PayloadProcessingStatusEnum::READY());
    }

    public function isProcessing(): bool
    {
        return $this->processing_status->equals(PayloadProcessingStatusEnum::PROCESSING());
    }

    public function isCollected(): bool
    {
        return $this->processing_status->equals(PayloadProcessingStatusEnum::COLLECTED());
    }

    public function isFailed(): bool
    {
        return $this->processing_status->equals(PayloadProcessingStatusEnum::FAILED());
    }

    public function markAsProcessing(): void
    {
        $this->update([
            self::PROCESSING_STATUS => PayloadProcessingStatusEnum::PROCESSING(),
        ]);
    }

    public function markAsDone(): void
    {
        $this->update([
            self::PROCESSING_STATUS => PayloadProcessingStatusEnum::DONE(),
            self::PROCESSED_AT => now(),
        ]);
    }

    public function dispatchToProcessor(): ?array
    {
        if (is_null($this->integrationType->getProcessor())) {
            throw new RuntimeException('No processor defined for this integration type');
        }

        if ($this->integrationType->isSynchronous()) {
            $processClass = $this->integrationType->getProcessor();

            /** @var PayloadProcessor $processor */
            $processor = new $processClass($this->id);

            return $processor->handle();
        } else {
            /** @var PayloadProcessor $processor */
            $processor = $this->integrationType->getProcessor();
            $processor::dispatch($this->id);
        }

        return null;
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            self::PROCESSING_STATUS => PayloadProcessingStatusEnum::FAILED(),
            self::ERROR => $error,
        ]);
    }
}

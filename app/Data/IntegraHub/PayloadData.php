<?php

namespace App\Data\IntegraHub;

use App\Enums\IntegraHub\PayloadProcessingStatusEnum;
use App\Enums\IntegraHub\PayloadStoringStatusEnum;
use App\Models\IntegraHub\IntegrationType;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class PayloadData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly int $integration_type_id,
        public readonly array $payload,
        public readonly string|null $payload_hash,
        public readonly Carbon|null $stored_at,
        #[WithCast(EnumCast::class)]
        public readonly PayloadStoringStatusEnum|null $storing_status,
        public readonly Carbon|null $processed_at,
        #[WithCast(EnumCast::class)]
        public readonly PayloadProcessingStatusEnum|null $processing_status,
        public readonly array|null|Optional $response,
        public readonly string|null|Optional $error,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
        //
    }

    public static function fromWebhookPayloadProcessor(IntegrationType $integrationType, array $payload): self
    {
        return self::from([
            'integration_type_id' => $integrationType->id,
            'payload' => $payload,
            'payload_hash' => md5(json_encode($payload)),
            'stored_at' => now(),
            'storing_status' => PayloadStoringStatusEnum::STORED,
            'processing_status' => PayloadProcessingStatusEnum::READY,
        ]);
    }
}

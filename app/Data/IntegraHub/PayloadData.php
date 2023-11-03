<?php

namespace App\Data\IntegraHub;

use App\Enums\IntegraHub\PayloadProcessingStatusEnum;
use App\Enums\IntegraHub\PayloadStoringStatusEnum;
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
        public readonly string|null|Optional $response,
        public readonly string|null|Optional $error,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
    }
}

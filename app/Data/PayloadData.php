<?php

namespace App\Data;

use App\Enums\PayloadProcessingStatusEnum;
use App\Enums\PayloadStoringStatusEnum;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class PayloadData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $integration_type_id,
        public readonly array $payload,
        public readonly ?Carbon $stored_at,
        #[WithCast(EnumCast::class)]
        public readonly ?PayloadStoringStatusEnum $stored_status,
        public readonly ?Carbon $processed_at,
        #[WithCast(EnumCast::class)]
        public readonly ?PayloadProcessingStatusEnum $processed_status,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {
    }
}

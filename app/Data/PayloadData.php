<?php

namespace App\Data;

use App\Enums\PayloadProcessedStatusEnum;
use App\Enums\PayloadStoredStatusEnum;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class PayloadData extends Data
{
    public function __construct(
        public readonly int $id,
        #[Rule('required')]
        public readonly int $integration_type_id,
        #[Rule('required')]
        public readonly string $payload,
        public readonly ?Carbon $stored_at,
        #[WithCast(EnumCast::class)]
        public readonly ?PayloadStoredStatusEnum $stored_status,
        public readonly ?Carbon $processed_at,
        #[WithCast(EnumCast::class)]
        public readonly ?PayloadProcessedStatusEnum $processed_status,
        public readonly ?Carbon $created_at,
        public readonly ?Carbon $updated_at,
    ) {
    }
}

<?php

namespace App\Data;

use App\Enums\PayloadProcessedStatusEnum;
use App\Enums\PayloadStoredStatusEnum;
use App\Models\IntegrationType;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class PayloadData extends Data
{
    public function __construct(
        public readonly ?int $id,
        #[Required, Exists(IntegrationType::TABLE_NAME, IntegrationType::ID)]
        public readonly int $integration_type_id,
        #[Required, ArrayType]
        public readonly array $payload,
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

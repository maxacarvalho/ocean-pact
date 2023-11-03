<?php

namespace App\Data\IntegraHub;

use App\Enums\IntegraHub\PayloadProcessingAttemptsStatusEnum;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PayloadProcessingAttemptData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly int $payload_id,
        #[WithCast(EnumCast::class)]
        public readonly PayloadProcessingAttemptsStatusEnum $status,
        public readonly string|null|Optional $message,
        public readonly string|null|Optional $response,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
        //
    }

    public static function makeForPayload(
        int $payloadId,
        PayloadProcessingAttemptsStatusEnum $status,
        ?string $response
    ): static {
        return new PayloadProcessingAttemptData(
            id: Optional::create(),
            payload_id: $payloadId,
            status: PayloadProcessingAttemptsStatusEnum::SUCCESS,
            message: Optional::create(),
            response: $response,
            created_at: Optional::create(),
            updated_at: Optional::create(),
        );
    }
}

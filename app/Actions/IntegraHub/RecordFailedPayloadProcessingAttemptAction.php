<?php

namespace App\Actions\IntegraHub;

use App\Data\IntegraHub\PayloadProcessingAttemptData;
use App\Enums\IntegraHub\PayloadProcessingAttemptsStatusEnum;
use App\Models\IntegraHub\PayloadProcessingAttempt;

class RecordFailedPayloadProcessingAttemptAction
{
    public function handle(int $payloadId, ?array $response): void
    {
        $data = PayloadProcessingAttemptData::makeForPayload(
            payloadId: $payloadId,
            status: PayloadProcessingAttemptsStatusEnum::FAILED,
            response: $response
        );

        PayloadProcessingAttempt::query()->create(
            $data->except(PayloadProcessingAttempt::CREATED_AT, PayloadProcessingAttempt::UPDATED_AT)->toArray()
        );
    }
}

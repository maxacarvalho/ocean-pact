<?php

namespace App\Actions\IntegraHub;

use App\Data\IntegraHub\PayloadProcessingAttemptData;
use App\Enums\IntegraHub\PayloadProcessingAttemptsStatusEnum;
use App\Models\IntegraHub\PayloadProcessingAttempt;

class RecordSuccessfulPayloadProcessingAttemptAction
{
    public function handle(int $payloadId, array $response): void
    {
        $data = PayloadProcessingAttemptData::makeForPayload(
            $payloadId,
            PayloadProcessingAttemptsStatusEnum::SUCCESS,
            $response
        );

        PayloadProcessingAttempt::query()->create(
            $data->except(PayloadProcessingAttempt::CREATED_AT, PayloadProcessingAttempt::UPDATED_AT)->toArray()
        );
    }
}

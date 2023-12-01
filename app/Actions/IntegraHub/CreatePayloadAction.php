<?php

namespace App\Actions\IntegraHub;

use App\Data\IntegraHub\PayloadData;
use App\Models\IntegraHub\Payload;

class CreatePayloadAction
{
    public function handle(PayloadData $payloadData): Payload
    {
        /** @var Payload $payload */
        $payload = Payload::query()->create(
            $payloadData->except(Payload::CREATED_AT, Payload::UPDATED_AT)->toArray()
        );

        return $payload;
    }
}

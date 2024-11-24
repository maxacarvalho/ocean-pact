<?php

namespace App\Actions\IntegraHub;

use App\Data\IntegraHub\PayloadInputData;
use App\Data\IntegraHub\PayloadSuccessResponseData;
use App\Jobs\PayloadProcessors\PayloadForwarderProcessorJob;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\Payload;
use JsonException;

readonly class HandlePayloadAction
{
    public function __construct(
        private HandlesSynchronousIntegrationPayloadAction $handlesSynchronousIntegrationPayloadAction,
    ) {}

    /** @throws JsonException */
    public function handle(
        IntegrationType $integrationType,
        Payload $payload,
        PayloadInputData $payloadInput
    ): PayloadSuccessResponseData {
        if ($integrationType->isForwardable() && $integrationType->isSynchronous()) {
            return $this->handlesSynchronousIntegrationPayloadAction->handle($payload, $integrationType);
        }

        if ($integrationType->isForwardable()) {
            PayloadForwarderProcessorJob::dispatch($payload->id);
        }

        return PayloadSuccessResponseData::from([
            'referenceId' => $payload->id,
            'details' => [],
        ]);
    }
}

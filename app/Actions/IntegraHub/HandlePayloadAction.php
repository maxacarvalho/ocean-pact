<?php

namespace App\Actions\IntegraHub;

use App\Data\IntegraHub\PayloadSuccessResponseData;
use App\Jobs\PayloadProcessors\PayloadForwarderProcessorJob;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\Payload;
use JsonException;

readonly class HandlePayloadAction
{
    public function __construct(
        private HandlesSynchronousIntegrationPayloadAction $handlesSynchronousIntegrationPayloadAction,
    ) {
    }

    /** @throws JsonException */
    public function handle(
        IntegrationType $integrationType,
        Payload $payload,
        array $payloadInput
    ): PayloadSuccessResponseData {
        if ($integrationType->isFetchable()) {
            return $this->handlesSynchronousIntegrationPayloadAction->handle(
                $payload,
                $integrationType,
                $payloadInput
            );
        }

        if ($integrationType->isForwardable() && $integrationType->isSynchronous()) {
            return $this->handlesSynchronousIntegrationPayloadAction->handle(
                $payload,
                $integrationType,
                $payloadInput
            );
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

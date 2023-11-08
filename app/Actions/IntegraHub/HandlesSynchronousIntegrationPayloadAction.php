<?php

namespace App\Actions\IntegraHub;

use App\Data\IntegraHub\PayloadSuccessResponseData;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\Payload;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

readonly class HandlesSynchronousIntegrationPayloadAction
{
    public function __construct(
        private RecordSuccessfulPayloadProcessingAttemptAction $recordSuccessfulPayloadProcessingAttemptAction,
    ) {
        //
    }

    public function handle(
        Payload $payload,
        IntegrationType $integrationType
    ): PayloadSuccessResponseData {
        $httpClient = Http::withOptions(['verify' => App::environment('production')])
            ->withToken(config('ocean-pact.temp_access_token'))
            ->withHeaders($integrationType->headers)
            ->throw();

        $httpClient->withBody(json_encode($payload->payload, JSON_THROW_ON_ERROR));

        $url = $integrationType->resolveTargetUrl($payload);

        $response = $httpClient->send($integrationType->type->value, $url)->json();

        $payload->markAsDone($response);

        $this->recordSuccessfulPayloadProcessingAttemptAction->handle(
            payloadId: $payload->id,
            response: $response
        );

        return PayloadSuccessResponseData::from([
            'referenceId' => $payload->id,
            'details' => $response,
        ]);
    }
}

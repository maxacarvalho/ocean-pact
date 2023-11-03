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

    public function handle(Payload $payload, IntegrationType $integrationType, array $payloadInput): PayloadSuccessResponseData
    {
        $httpClient = Http::withOptions(['verify' => App::environment('production')])
            ->withToken(config('ocean-pact.temp_access_token'))
            ->withHeaders($integrationType->headers)
            ->withBody(json_encode($payloadInput, JSON_THROW_ON_ERROR))
            ->throw();

        $response = $httpClient->send($integrationType->type->value, $integrationType->target_url)->json();

        $payload->markAsDone(json_encode($response, JSON_THROW_ON_ERROR));

        $this->recordSuccessfulPayloadProcessingAttemptAction->handle(
            payloadId: $payload->id,
            response: json_encode($response, JSON_THROW_ON_ERROR)
        );

        return PayloadSuccessResponseData::from([
            'referenceId' => $payload->id,
            'details' => $response,
        ]);
    }
}

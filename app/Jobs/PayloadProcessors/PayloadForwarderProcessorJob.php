<?php

namespace App\Jobs\PayloadProcessors;

use App\Actions\IntegraHub\RecordFailedPayloadProcessingAttemptAction;
use App\Actions\IntegraHub\RecordSuccessfulPayloadProcessingAttemptAction;
use App\Models\IntegraHub\Payload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PayloadForwarderProcessorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $payloadId
    ) {
    }

    public function handle(
        RecordSuccessfulPayloadProcessingAttemptAction $recordSuccessfulPayloadProcessingAttemptAction,
    ): void {
        /** @var Payload $payload */
        $payload = Payload::query()
            ->with(Payload::RELATION_INTEGRATION_TYPE)
            ->findOrFail($this->payloadId);

        $httpClient = Http::withOptions(['verify' => App::environment('production')])
            ->withToken(config('ocean-pact.temp_access_token'))
            ->withHeaders($payload->integrationType->headers)
            ->withBody(json_encode($payload->payload, JSON_THROW_ON_ERROR))
            ->throw();

        $response = $httpClient->send(
            method: $payload->integrationType->type->value,
            url: $payload->integrationType->target_url
        )->json();

        $payload->markAsDone(
            json_encode($response, JSON_THROW_ON_ERROR)
        );

        $recordSuccessfulPayloadProcessingAttemptAction->handle(
            payloadId: $payload->id,
            response: $response
        );
    }

    public function failed(Throwable $exception): void
    {
        Log::error('PayloadForwarderProcessorJob failed', [
            'payload_id' => $this->payloadId,
            'exception_message' => $exception->getMessage(),
            'namespace' => __CLASS__,
        ]);

        try {
            /** @var Payload $payload */
            $payload = Payload::query()
                ->with(Payload::RELATION_INTEGRATION_TYPE)
                ->findOrFail($this->payloadId);

            $payload->markAsFailed($exception->getMessage(), null);
        } catch (Throwable) {
            //
        }

        $this->getRecordFailedPayloadProcessingAttemptAction()->handle(
            payloadId: $this->payloadId,
            response: json_encode($exception->getMessage()),
        );
    }

    protected function getRecordFailedPayloadProcessingAttemptAction(): RecordFailedPayloadProcessingAttemptAction
    {
        /** @var RecordFailedPayloadProcessingAttemptAction $action */
        $action = app(RecordFailedPayloadProcessingAttemptAction::class);

        return $action;
    }
}

<?php

namespace App\Jobs\PayloadProcessors;

use App\Actions\IntegraHub\RecordFailedPayloadProcessingAttemptAction;
use App\Actions\IntegraHub\RecordSuccessfulPayloadProcessingAttemptAction;
use App\Enums\IntegraHub\IntegrationHandlingTypeEnum;
use App\Enums\IntegraHub\IntegrationTypeEnum;
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

        $fields = $payload->integrationType->fields;
        $payloadData = $payload->payload;
        foreach ($fields as $field) {
            if (array_key_exists($field->field_name, $payloadData)) {
                $key = $field->alternate_name ?? $field->field_name;
                $value = $payloadData[$field->field_name];
                unset($payloadData[$field->field_name]);
                $payloadData[$key] = $value;
            }
        }

        $httpClient = Http::withOptions(['verify' => App::environment('production')])
            ->withHeaders($this->getHeaders($payload))
            ->withBody(json_encode($payloadData, JSON_THROW_ON_ERROR))
            ->throw();

        if ($payload->integrationType->handling_type->equals(IntegrationHandlingTypeEnum::STORE_AND_SEND)) {
            $httpClient->withToken(config('ocean-pact.temp_access_token'));
        }

        $response = $httpClient->send(
            method: $this->getMethod($payload),
            url: $this->getTargetUrl($payload),
        )->json();

        $payload->markAsDone($response);

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

    private function getTargetUrl(Payload $payload): string
    {
        if ($payload->integrationType->handling_type->equals(IntegrationHandlingTypeEnum::FETCH_AND_SEND)) {
            return $payload->integrationType->forward_url;
        }

        return $payload->integrationType->resolveTargetUrl($payload);
    }

    private function getHeaders(Payload $payload): array
    {
        if ($payload->integrationType->handling_type->equals(IntegrationHandlingTypeEnum::FETCH_AND_SEND)) {
            return $payload->integrationType->getForwardHeaders();
        }

        return $payload->integrationType->getHeaders();
    }

    private function getMethod(Payload $payload): string
    {
        if ($payload->integrationType->handling_type->equals(IntegrationHandlingTypeEnum::FETCH_AND_SEND)) {
            return IntegrationTypeEnum::POST->value;
        }

        return $payload->integrationType->type->value;
    }
}

<?php

namespace App\Jobs\PayloadProcessors;

use App\Enums\IntegrationTypeEnum;
use App\Models\Payload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ForwardPayloadProcessorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $payloadId
    ) {
    }

    public function handle(): void
    {
        /** @var Payload $payload */
        $payload = Payload::query()
            ->with(Payload::RELATION_INTEGRATION_TYPE)
            ->findOrFail($this->payloadId);

        $integrationType = $payload->integrationType;
        $method = $integrationType->type->value;
        $targetUrl = $integrationType->target_url;

        $httpClient = Http::withOptions([
            'verify' => App::environment('production'),
        ])->throw();

        $httpClient->withToken(config('ocean-pact.temp_access_token'));

        if ($integrationType->type->equals(IntegrationTypeEnum::GET)) {
        }

        if ($integrationType->type->equals(IntegrationTypeEnum::POST)) {
            $httpClient->post($targetUrl, $payload->payload);
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('ForwardPayloadProcessorJob failed', [
            'payload_id' => $this->payloadId,
            'exception_message' => $exception->getMessage(),
            'namespace' => __CLASS__,
        ]);
    }
}

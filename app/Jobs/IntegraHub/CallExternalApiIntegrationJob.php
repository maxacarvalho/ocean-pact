<?php

namespace App\Jobs\IntegraHub;

use App\Models\IntegraHub\IntegrationType;
use App\Services\PayloadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallExternalApiIntegrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private IntegrationType $integrationType
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PayloadService $payloadService): void
    {
        Log::info('Integration type ' . $this->integrationType->code . ' is due');
        $this->integrationType->markAsRunning();

        $httpClient = Http::withOptions(['verify' => App::environment('production')])
            ->withHeaders($this->integrationType->headers)
            ->throw();

        $url = $this->integrationType->target_url;

        $response = $httpClient->send(
            method: $this->integrationType->type->value,
            url: $url
        )->json();

        $payload = [
            'payload' => $response,
        ];

        Log::info('Response from '. $this->integrationType->target_url . ': ' . json_encode($payload));
        $payloadService->handlePayload($this->integrationType, $payload);

        $this->integrationType->markAsStopped();
    }
}

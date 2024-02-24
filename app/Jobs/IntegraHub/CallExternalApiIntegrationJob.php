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
use Throwable;

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
        $this->integrationType->markAsRunning();

        $httpClient = Http::withOptions(['verify' => App::environment('production')])
            ->withHeaders($this->integrationType->getHeaders())
            ->throw();

        $url = $this->integrationType->target_url;

        $response = null;
        try {
            $response = $httpClient->send(
                method: $this->integrationType->type->value,
                url: $url
            )->json();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        } finally {
            $this->integrationType->markAsStopped();
        }

        if (! $response) {
            Log::info('No response from ' . $this->integrationType->target_url);
            return;
        }

        $payload = [
            'payload' => $response,
        ];

        $payloadService->handlePayload($this->integrationType, $payload);
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        $this->integrationType->markAsStopped();

        Log::error('CallExternalApiIntegrationJob exception', [
            'exception_message' => $exception->getMessage(),
            'integration_type' => $this->integrationType->code,
        ]);

        report($exception);
    }
}

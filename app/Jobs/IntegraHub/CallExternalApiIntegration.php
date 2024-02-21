<?php

namespace App\Jobs;

use App\Models\IntegraHub\IntegrationType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class CallExternalApiIntegration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public IntegrationType $integrationType,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $httpClient = Http::withOptions(['verify' => App::environment('production')])
            ->withHeaders($this->integrationType->headers)
            ->throw();

        $url = $this->integrationType->target_url;

        $response = $httpClient->send(
            method: $this->integrationType->type->value,
            url: $url
        )->json();

        // Create payload?

        var_dump($response);
    }
}

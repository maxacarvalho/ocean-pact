<?php

namespace App\Console\Commands;

use App\Jobs\CallExternalApiIntegration;
use App\Models\IntegraHub\IntegrationType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckIntegrationTypeSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integration-type:check-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check integration scheduling settings and run if scheduled.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $integrations = IntegrationType::all();

        foreach ($integrations as $integration) {
            Log::info('Checking integration type '.$integration->code);
            if ($integration->isDue()) {
                Log::info('Integration type '.$integration->code.' is due');
                // Dispatch job to perform the call to the integration target URL
                // dispatch(new CallExternalApiIntegration($integration));

                $httpClient = Http::withOptions(['verify' => App::environment('production')])
                    ->withHeaders($integration->headers)
                    ->throw();

                $url = $integration->target_url;

                $response = $httpClient->send(
                    method: $integration->type->value,
                    url: $url
                )->json();

                // Create payload?
                Log::info('Response from '.$integration->target_url.': '.json_encode($response));
            }
        }
    }
}

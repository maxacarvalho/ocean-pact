<?php

namespace App\Console\Commands;

use App\Enums\IntegraHub\IntegrationHandlingTypeEnum;
use App\Jobs\IntegraHub\CallExternalApiIntegrationJob;
use App\Models\IntegraHub\IntegrationType;
use Illuminate\Console\Command;

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
        /** @var Collection<IntegrationType> $integrations */
        $integrations = IntegrationType::query()
            ->where('handling_type', IntegrationHandlingTypeEnum::FETCH)
            ->get();

        foreach ($integrations as $integration) {
            if ($integration->isDue()) {
                dispatch(new CallExternalApiIntegrationJob($integration));
            }
        }
    }
}

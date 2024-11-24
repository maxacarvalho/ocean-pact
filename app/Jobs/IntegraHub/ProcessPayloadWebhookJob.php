<?php

namespace App\Jobs\IntegraHub;

use App\Actions\IntegraHub\CreatePayloadAction;
use App\Data\IntegraHub\PayloadData;
use App\Models\IntegraHub\IntegrationType;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessPayloadWebhookJob extends ProcessWebhookJob
{
    public function handle(CreatePayloadAction $createPayloadAction): void
    {
        $urlParts = parse_url($this->webhookCall->url);
        parse_str($urlParts['query'], $query);

        $integrationTypeCode = $query['integration-type-code'] ?? null;

        if ($integrationTypeCode === null) {
            $this->delete();
        }

        /** @var IntegrationType $integrationType */
        $integrationType = IntegrationType::query()
            ->where(IntegrationType::CODE, $integrationTypeCode)
            ->firstOrFail();

        $createPayloadAction->handle(
            PayloadData::fromWebhookPayloadProcessor(
                $integrationType,
                $this->webhookCall->payload
            )
        );
    }
}

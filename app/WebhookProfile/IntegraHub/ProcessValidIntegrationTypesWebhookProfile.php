<?php

namespace App\WebhookProfile\IntegraHub;

use App\Models\IntegraHub\IntegrationType;
use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

class ProcessValidIntegrationTypesWebhookProfile implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        return IntegrationType::query()
            ->where(IntegrationType::CODE, '=', $request->query('integration-type-code'))
            ->exists();
    }
}

<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\RequestNewOfferData;
use App\Models\QuotesPortal\Quote;
use Spatie\WebhookServer\WebhookCall;

class RequestNewOfferAction
{
    public function handle(int $quoteId): void
    {
        /** @var Quote $quote */
        $quote = Quote::query()->findOrFail($quoteId);

        $data = RequestNewOfferData::from($quote);

        WebhookCall::create()
            ->url(config('integra-hub.base_url').'/integra-hub/webhooks/payload?integration-type-code=nova-proposta')
            ->payload($data->toArray())
            ->useSecret(config('integra-hub.webhook-secret'))
            ->dispatchSync();
    }
}

<?php

namespace App\Listeners;

use App\Events\QuoteRespondedEvent;
use App\Jobs\CreateQuoteRespondedPayloadJob;
use App\Models\Quote;

class PrepareRespondedQuoteForCollectionListener
{
    public function handle(QuoteRespondedEvent $event): void
    {
        $quote = Quote::query()->findOrFail($event->quoteId);

        if ($quote->isResponded()) {
            CreateQuoteRespondedPayloadJob::dispatch($event->quoteId);
        }
    }
}

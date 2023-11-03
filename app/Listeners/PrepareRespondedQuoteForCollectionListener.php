<?php

namespace App\Listeners;

use App\Events\QuoteRespondedEvent;
use App\Jobs\CreateQuoteRespondedPayloadJob;
use App\Models\QuotesPortal\Quote;

class PrepareRespondedQuoteForCollectionListener
{
    public function handle(QuoteRespondedEvent $event): void
    {
        /** @var Quote $quote */
        $quote = Quote::query()->findOrFail($event->quoteId);

        if ($quote->isResponded()) {
            CreateQuoteRespondedPayloadJob::dispatch($event->quoteId);
        }
    }
}

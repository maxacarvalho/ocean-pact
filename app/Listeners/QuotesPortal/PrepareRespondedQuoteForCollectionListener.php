<?php

namespace App\Listeners\QuotesPortal;

use App\Events\QuotePortal\QuoteRespondedEvent;
use App\Jobs\QuotesPortal\CreateQuoteRespondedPayloadJob;
use App\Models\QuotesPortal\Quote;
use Illuminate\Contracts\Queue\ShouldQueue;

class PrepareRespondedQuoteForCollectionListener implements ShouldQueue
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

<?php

namespace App\Listeners\QuotesPortal;

use App\Actions\QuotesPortal\SendQuoteProposalNotificationAction;
use App\Events\QuotePortal\QuoteProposalCreatedEvent;
use App\Models\QuotesPortal\Quote;

readonly class QuoteProposalCreatedEventListener
{
    public function __construct(
        private SendQuoteProposalNotificationAction $sendQuoteProposalNotificationAction
    ) {
        //
    }

    public function handle(QuoteProposalCreatedEvent $event): void
    {
        /** @var Quote $quote */
        $quote = Quote::query()->findOrFail($event->quoteId);

        $this->sendQuoteProposalNotificationAction->handle(
            quote: $quote
        );
    }
}

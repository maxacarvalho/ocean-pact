<?php

namespace App\Listeners\QuotesPortal;

use App\Actions\QuotesPortal\CreateAndSendUserInvitationAction;
use App\Events\QuotePortal\QuoteCreatedEvent;
use App\Models\QuotesPortal\Quote;
use Illuminate\Contracts\Queue\ShouldQueue;

readonly class QuoteCreatedEventListener implements ShouldQueue
{
    public function __construct(
        private CreateAndSendUserInvitationAction $createAndSendUserInvitationAction
    ) {
        //
    }

    public function handle(QuoteCreatedEvent $event): void
    {
        /** @var Quote $quote */
        $quote = Quote::query()
            ->with([Quote::RELATION_COMPANY, Quote::RELATION_SUPPLIER])
            ->findOrFail($event->quoteId);

        $this->createAndSendUserInvitationAction->handle(
            quote: $quote
        );
    }
}

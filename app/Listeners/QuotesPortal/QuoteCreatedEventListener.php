<?php

namespace App\Listeners\QuotesPortal;

use App\Actions\QuotesPortal\CreateAndSendSupplierInvitationAction;
use App\Events\QuotePortal\QuoteCreatedEvent;
use App\Models\QuotesPortal\Quote;
use Illuminate\Contracts\Queue\ShouldQueue;

readonly class QuoteCreatedEventListener implements ShouldQueue
{
    public function __construct(
        private CreateAndSendSupplierInvitationAction $createAndSendSupplierInvitationAction
    ) {
        //
    }

    public function handle(QuoteCreatedEvent $event): void
    {
        /** @var Quote $quote */
        $quote = Quote::query()
            ->with([Quote::RELATION_COMPANY, Quote::RELATION_SUPPLIER])
            ->findOrFail($event->quoteId);

        $this->createAndSendSupplierInvitationAction->handle($quote);
    }
}

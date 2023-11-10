<?php

namespace App\Listeners\QuotesPortal;

use App\Actions\QuotesPortal\CreateAndSendBuyerInvitationAction;
use App\Events\QuotePortal\BuyerCreatedEvent;
use App\Models\User;

readonly class BuyerCreatedEventListener
{
    public function __construct(
        private CreateAndSendBuyerInvitationAction $createAndSendBuyerInvitationAction
    ) {
        //
    }

    public function handle(BuyerCreatedEvent $event): void
    {
        /** @var User $buyer */
        $buyer = User::query()->findOrFail($event->buyerId);

        $this->createAndSendBuyerInvitationAction->handle($buyer);
    }
}

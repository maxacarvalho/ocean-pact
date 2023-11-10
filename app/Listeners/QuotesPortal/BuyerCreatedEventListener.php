<?php

namespace App\Listeners\QuotesPortal;

use App\Actions\QuotesPortal\CreateAndSendUserInvitationAction;
use App\Events\QuotePortal\BuyerCreatedEvent;
use App\Models\User;

readonly class BuyerCreatedEventListener
{
    public function __construct(
        private CreateAndSendUserInvitationAction $createAndSendUserInvitationAction
    ) {
        //
    }

    public function handle(BuyerCreatedEvent $event): void
    {
        /** @var User $buyer */
        $buyer = User::query()->findOrFail($event->buyerId);

        $this->createAndSendUserInvitationAction->handle(
            buyer: $buyer
        );
    }
}

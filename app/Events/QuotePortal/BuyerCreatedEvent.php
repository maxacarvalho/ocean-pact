<?php

namespace App\Events\QuotePortal;

use Illuminate\Foundation\Events\Dispatchable;

class BuyerCreatedEvent
{
    use Dispatchable;

    public function __construct(
        public int $buyerId,
    ) {
        //
    }
}

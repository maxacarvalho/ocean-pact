<?php

namespace App\Events\QuotePortal;

use Illuminate\Foundation\Events\Dispatchable;

class QuoteProposalCreatedEvent
{
    use Dispatchable;

    public function __construct(
        public int $quoteId
    ) {
        //
    }
}

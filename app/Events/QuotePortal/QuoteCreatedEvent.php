<?php

namespace App\Events\QuotePortal;

use Illuminate\Foundation\Events\Dispatchable;

class QuoteCreatedEvent
{
    use Dispatchable;

    public function __construct(
        public int $quoteId
    ) {
        //
    }
}

<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class QuoteRespondedEvent
{
    use Dispatchable;

    public function __construct(
        public int $quoteId
    ) {
    }
}

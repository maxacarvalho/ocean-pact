<?php

namespace App\Data\QuotesPortal\Quote;

use Spatie\LaravelData\Data;

class StoreQuoteErrorResponseData extends Data
{
    public function __construct(
        public string $title,
        public array $errors,
    ) {
        //
    }
}

<?php

namespace App\Data\QuotesPortal\StoreQuotePayloadData;

use Spatie\LaravelData\Data;

class PaymentConditionData extends Data
{
    public function __construct(
        public readonly string $code,
        public readonly string $description,
    ) {
        //
    }
}

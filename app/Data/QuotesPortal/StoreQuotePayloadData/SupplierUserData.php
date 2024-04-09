<?php

namespace App\Data\QuotesPortal\StoreQuotePayloadData;

use Spatie\LaravelData\Data;

class SupplierUserData extends Data
{
    public function __construct(
        public readonly string $code,
    ) {
        //
    }
}

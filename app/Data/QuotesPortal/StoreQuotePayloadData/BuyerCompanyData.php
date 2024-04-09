<?php

namespace App\Data\QuotesPortal\StoreQuotePayloadData;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class BuyerCompanyData extends Data
{
    public function __construct(
        #[MapInputName('buyer_code')]
        public readonly string $buyerCode
    ) {
        //
    }
}

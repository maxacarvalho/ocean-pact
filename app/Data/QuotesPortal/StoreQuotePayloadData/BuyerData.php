<?php

namespace App\Data\QuotesPortal\StoreQuotePayloadData;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class BuyerData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        #[MapInputName('buyer_company')]
        public readonly BuyerCompanyData $buyerCompany
    ) {
        //
    }
}

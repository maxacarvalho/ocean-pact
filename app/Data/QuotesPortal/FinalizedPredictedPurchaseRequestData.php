<?php

namespace App\Data\QuotesPortal;

use Spatie\LaravelData\Data;

class FinalizedPredictedPurchaseRequestData extends Data
{
    public function __construct(
        public readonly string $quote_number,
        public readonly CompanyData $company,
        public readonly BuyerData $buyer,
        public readonly array $suppliers
    ) {
        //
    }
}

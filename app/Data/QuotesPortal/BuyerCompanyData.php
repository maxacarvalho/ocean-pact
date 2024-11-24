<?php

namespace App\Data\QuotesPortal;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class BuyerCompanyData extends Data
{
    public function __construct(
        public readonly int|Optional $company_id,
        public readonly int|Optional $user_id,
        public readonly ?string $buyer_code,
    ) {
        //
    }
}

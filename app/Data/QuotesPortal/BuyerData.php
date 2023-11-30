<?php

namespace App\Data\QuotesPortal;

use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Quote;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class BuyerData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly string $name,
        public readonly string $email,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
        public readonly BuyerCompanyData|Optional $buyer_company,
    ) {
        //
    }

    public static function fromQuote(Quote $quote): BuyerData
    {
        $buyer = $quote->buyer;

        return new self(
            id: $buyer->id,
            name: $buyer->name,
            email: $buyer->email,
            created_at: $buyer->created_at,
            updated_at: $buyer->updated_at,
            buyer_company: BuyerCompanyData::from(
                $buyer->companies()->where(Company::ID, '=', $quote->company_id)->first()?->buyer_company
            ),
        );
    }
}

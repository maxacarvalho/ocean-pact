<?php

namespace App\Data\QuotesPortal;

use App\Utils\Money;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCastable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProductData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly string|Optional $company_code,
        public readonly string|null|Optional $company_code_branch,
        public readonly string|Optional $code,
        public readonly string|Optional $description,
        public readonly string|Optional $measurement_unit,
        #[WithCastable(Money::class)]
        public readonly Money|Optional $last_price,
        #[WithCastable(Money::class)]
        public readonly Money|Optional $smallest_price,
        public readonly int|Optional $smallest_eta,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
        //
    }
}

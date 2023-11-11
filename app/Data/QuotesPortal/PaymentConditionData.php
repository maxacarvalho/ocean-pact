<?php

namespace App\Data\QuotesPortal;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PaymentConditionData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly string|Optional $company_code,
        public readonly string|null|Optional $company_code_branch,
        public readonly string $code,
        public readonly string $description,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
        //
    }
}

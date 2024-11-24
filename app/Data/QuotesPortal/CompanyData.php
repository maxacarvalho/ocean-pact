<?php

namespace App\Data\QuotesPortal;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CompanyData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly string $code,
        public readonly string $code_branch,
        public readonly string $branch,
        public readonly string $name,
        public readonly string $business_name,
        public readonly ?string $code_code_branch_and_business_name,
        public readonly ?string $code_code_branch_and_branch,
        public readonly ?string $code_and_business_name,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
        //
    }
}

<?php

namespace App\Data\QuotesPortal;

use App\Enums\QuotesPortal\BudgetStatusEnum;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class BudgetData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly string $company_code,
        public readonly string|null|Optional $company_code_branch,
        public readonly string $budget_number,
        #[WithCast(EnumCast::class)]
        public readonly BudgetStatusEnum $status,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
        //
    }
}

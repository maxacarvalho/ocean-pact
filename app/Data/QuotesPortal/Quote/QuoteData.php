<?php

namespace App\Data\QuotesPortal\Quote;

use App\Enums\QuotesPortal\QuoteStatusEnum;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class QuoteData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly string $company_code,
        public readonly string|null $company_code_branch,
        public readonly int $supplier_id,
        public readonly int $payment_condition_id,
        public readonly int $buyer_id,
        public readonly int $budget_id,
        public readonly string $quote_number,
        public readonly Carbon|null $valid_until,
        #[WithCast(EnumCast::class)]
        public readonly QuoteStatusEnum $status,
        public readonly string|null $comments,
        public readonly int $expenses,
        public readonly int $freight_cost,
        #[WithCast(EnumCast::class)]
        public readonly QuoteStatusEnum|null $freight_type,
        public readonly int|null $currency_id,
        public readonly Carbon|null $created_at,
        public readonly Carbon|null $updated_at,
    ) {
        //
    }
}

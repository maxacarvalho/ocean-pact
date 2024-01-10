<?php

namespace App\Data\QuotesPortal;

use App\Utils\Money;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCastable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PredictedPurchaseRequestData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly int $company_id,
        public readonly string $quote_number,
        public readonly int $quote_id,
        public readonly int $supplier_id,
        public readonly int $product_id,
        public readonly string $item,
        public readonly int $quote_item_id,
        public readonly Carbon $delivery_date,
        #[WithCastable(Money::class)]
        public readonly Money|Optional $price,
        #[WithCastable(Money::class)]
        public readonly Money|Optional $last_price,
        public readonly Carbon $necessity_date,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
        //
    }
}

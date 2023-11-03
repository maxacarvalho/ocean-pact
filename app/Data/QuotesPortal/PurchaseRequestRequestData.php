<?php

namespace App\Data\QuotesPortal;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PurchaseRequestRequestData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly int $quote_id,
        public readonly string $purchase_request_number,
        public readonly Carbon|null|Optional $sent_at,
        public readonly Carbon|null|Optional $viewed_at,
        public readonly string|null $file,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
    }
}

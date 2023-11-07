<?php

namespace App\Data\QuotesPortal;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class BuyerData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string|null $buyer_code,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
        //
    }
}

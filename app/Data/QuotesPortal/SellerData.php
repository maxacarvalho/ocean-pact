<?php

namespace App\Data\QuotesPortal;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class SellerData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string|Optional $seller_code,
        public readonly bool $active,
        public readonly Carbon|null|Optional $email_verified_at,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
        //
    }
}

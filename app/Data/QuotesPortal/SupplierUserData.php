<?php

namespace App\Data\QuotesPortal;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class SupplierUserData extends Data
{
    public function __construct(
        public int|Optional $supplier_id,
        public int|Optional $user_id,
        public string|null $code,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
        //
    }
}

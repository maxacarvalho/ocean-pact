<?php

namespace App\Data\QuotesPortal;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CurrencyData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly string|Optional $company_code,
        public readonly int $protheus_currency_id,
        public readonly string $description,
        public readonly string $protheus_code,
        public readonly string $protheus_acronym,
        public readonly string|Optional $iso_code,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
        //
    }
}

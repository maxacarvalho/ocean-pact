<?php

namespace App\Data\QuotesPortal;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class SupplierData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly string $company_code,
        public readonly string|null $company_code_branch,
        public readonly string $code,
        public readonly string $store,
        public readonly string $name,
        public readonly string $business_name,
        public readonly string|null $address,
        public readonly string|null $number,
        public readonly string|null $state_code,
        public readonly string|null $postal_code,
        public readonly string|null $cnpj_cpf,
        public readonly string|null $phone_code,
        public readonly string|null $phone_number,
        public readonly string $contact,
        public readonly string $email,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
    }
}

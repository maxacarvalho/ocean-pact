<?php

namespace App\Data\QuotesPortal;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;

class SupplierData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly string|Optional $company_code,
        public readonly ?string $company_code_branch,
        public readonly string $code,
        public readonly string $store,
        public readonly string $name,
        public readonly string $business_name,
        public readonly ?string $address,
        public readonly ?string $number,
        public readonly ?string $state_code,
        public readonly ?string $postal_code,
        public readonly ?string $cnpj_cpf,
        public readonly ?string $phone_code,
        public readonly ?string $phone_number,
        public readonly string|null|Optional $contact,
        public readonly string|null|Optional $email,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
        // Relations
        #[DataCollectionOf(CompanyData::class)]
        public readonly Lazy|DataCollection|Optional $companies,
        #[DataCollectionOf(SellerData::class)]
        public readonly Lazy|DataCollection|Optional $sellers
    ) {}
}

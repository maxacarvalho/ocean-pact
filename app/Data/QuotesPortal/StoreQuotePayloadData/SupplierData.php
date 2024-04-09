<?php

namespace App\Data\QuotesPortal\StoreQuotePayloadData;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class SupplierData extends Data
{
    /** @var Collection<int, SellerData> */
    public Collection $sellers;

    public function __construct(
        public readonly string $store,
        public readonly string $code,
        public readonly string $name,
        #[MapInputName('business_name')]
        public readonly string|null|Optional $businessName,
        public readonly string|null|Optional $address,
        public readonly string|null|Optional $number,
        #[MapInputName('state_code')]
        public readonly string|null|Optional $stateCode,
        #[MapInputName('postal_code')]
        public readonly string|null|Optional $postalCode,
        #[MapInputName('cnpj_cpf')]
        public readonly string|null|Optional $cnpjCpf,
        #[MapInputName('phone_code')]
        public readonly string|null|Optional $phoneCode,
        #[MapInputName('phone_number')]
        public readonly string|null|Optional $phoneNumber,
    ) {
        //
    }
}

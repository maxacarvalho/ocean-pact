<?php

namespace App\Data\QuotesPortal\StoreQuotePayloadData;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class SellerData extends Data
{
    public function __construct(
        public readonly bool $active,
        public readonly string $name,
        public readonly string $email,
        #[MapInputName('supplier_user')]
        public readonly SupplierUserData $supplierUser,
    ) {
        //
    }
}

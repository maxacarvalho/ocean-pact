<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class PurchaseRequestRequestData extends Data
{
    public function __construct(
        #[MapInputName('ID_COTACAO')]
        public int $quote_id,
        #[MapInputName('NUMERO_PEDIDO')]
        public string $purchase_request_number,
        #[MapInputName('ARQUIVO')]
        public string $file,
    ) {
    }
}

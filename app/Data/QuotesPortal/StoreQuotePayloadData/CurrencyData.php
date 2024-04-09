<?php

namespace App\Data\QuotesPortal\StoreQuotePayloadData;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class CurrencyData extends Data
{
    public function __construct(
        #[MapInputName('protheus_currency_id')]
        public readonly int $protheusCurrencyId,
        #[MapInputName('protheus_acronym')]
        public readonly string $protheusAcronym,
        #[MapInputName('protheus_code')]
        public readonly string $protheusCode,
        public readonly string $description,
    ) {
        //
    }
}

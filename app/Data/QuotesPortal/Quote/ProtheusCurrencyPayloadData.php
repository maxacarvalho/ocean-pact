<?php

namespace App\Data\QuotesPortal\Quote;

use App\Models\QuotesPortal\Quote;
use Spatie\LaravelData\Attributes\Validation\Alpha;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class ProtheusCurrencyPayloadData extends Data
{
    public function __construct(
        #[Required]
        public readonly string $CODIGO,
        #[Required]
        public readonly string $MOEDA,
        #[Required, Alpha, Max(3)]
        public readonly string $SIGLA,
        #[Required, Max(3)]
        public readonly string $EMPRESA,
        #[Required]
        public readonly string $DESCRICAO
    ) {
        //
    }

    public static function fromQuote(Quote $quote): static
    {
        return new static(
            CODIGO: $quote->currency->protheus_code,
            MOEDA: $quote->currency->protheus_currency_id,
            SIGLA: $quote->currency->protheus_acronym,
            EMPRESA: $quote->currency->company_code,
            DESCRICAO: $quote->currency->description
        );
    }
}

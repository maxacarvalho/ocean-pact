<?php

namespace App\Data\Protheus\Quote;

use App\Models\Quote;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProtheusPaymentConditionData extends Data
{
    public function __construct(
        public string $CODIGO,
        public string $DESCRICAO,
        public string|null|Optional $FILIAL
    ) {
    }

    public static function fromQuote(Quote $quote): self
    {
        return new self(
            CODIGO: $quote->paymentCondition->code,
            DESCRICAO: $quote->paymentCondition->description,
            FILIAL: $quote->paymentCondition->company ? $quote->paymentCondition->company->code_branch : null
        );
    }
}

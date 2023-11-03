<?php

namespace App\Data\QuotesPortal\Quote;

use App\Models\QuotesPortal\Quote;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProtheusPaymentConditionData extends Data
{
    public function __construct(
        #[Required]
        public readonly string $CODIGO,
        #[Required]
        public readonly string $DESCRICAO,
        public readonly string|null|Optional $FILIAL
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

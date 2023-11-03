<?php

namespace App\Data\QuotesPortal\Quote;

use App\Models\QuotesPortal\Quote;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProtheusSupplierPayloadData extends Data
{
    public function __construct(
        #[Required]
        public readonly string $CODIGO,
        #[Required]
        public readonly string $LOJA,
        #[Required]
        public readonly string $NOME_FORNECEDOR,
        #[Required]
        public readonly string $NOME_FANTASIA,
        #[Required]
        public readonly string $UF,
        #[Email]
        public readonly string $EMAIL,
        public readonly string $CONTATO,
        public readonly string|null|Optional $FILIAL
    ) {
    }

    public static function fromQuote(Quote $quote): self
    {
        return new self(
            CODIGO: $quote->supplier->code,
            LOJA: $quote->supplier->store,
            NOME_FORNECEDOR: $quote->supplier->name,
            NOME_FANTASIA: $quote->supplier->business_name,
            UF: $quote->supplier->state_code,
            EMAIL: $quote->supplier->email,
            CONTATO: $quote->supplier->contact,
            FILIAL: $quote->supplier->company ? $quote->supplier->company->code_branch : null
        );
    }
}

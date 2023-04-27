<?php

namespace App\Data\Protheus\Quote;

use App\Models\Quote;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProtheusSupplierPayloadData extends Data
{
    public function __construct(
        public string $CODIGO,
        public string $LOJA,
        public string $NOME_FORNECEDOR,
        public string $NOME_FANTASIA,
        public string $UF,
        public string $EMAIL,
        public string $CONTATO,
        public string|null|Optional $FILIAL
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

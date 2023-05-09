<?php

namespace App\Data\Protheus\Quote;

use App\Models\QuoteItem;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProtheusProductPayloadData extends Data
{
    public function __construct(
        public string $CODIGO,
        public string $DESCRICAO,
        public string $UNIDADE_MEDIDA,
        public string|null|Optional $FILIAL
    ) {
    }

    public static function fromQuoteItem(QuoteItem $quoteItem): self
    {
        return new self(
            CODIGO: $quoteItem->product->code,
            DESCRICAO: $quoteItem->product->description,
            UNIDADE_MEDIDA: $quoteItem->product->measurement_unit,
            FILIAL: $quoteItem->product->company_code_branch
        );
    }
}

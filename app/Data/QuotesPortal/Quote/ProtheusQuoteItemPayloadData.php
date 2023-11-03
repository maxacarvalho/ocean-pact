<?php

namespace App\Data\QuotesPortal\Quote;

use App\Models\QuotesPortal\QuoteItem;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProtheusQuoteItemPayloadData extends Data
{
    public function __construct(
        #[Required]
        public readonly string $DESCRICAO,
        #[Required]
        public readonly string $UNIDADE_MEDIDA,
        #[Required]
        public readonly string $ITEM,
        #[Required, Numeric]
        public readonly float $QUANTIDADE,
        #[Required, Numeric]
        public readonly string $PRECO_UNITARIO,
        #[Numeric]
        public readonly string|Optional $IPI,
        #[Numeric]
        public readonly string|Optional $ICMS,
        public readonly string|null $DATA_ENTREGA, // deprecated
        #[Numeric]
        public readonly int|null|Optional $ENTREGA_EM_DIAS,
        public readonly bool|null|Optional $INCLUIR_NA_COTACAO,
        public readonly ?string $OBS,
        #[Required]
        public readonly ProtheusProductPayloadData $PRODUTO
    ) {
    }

    public static function fromQuoteItem(QuoteItem $quoteItem): self
    {
        return new self(
            DESCRICAO: $quoteItem->description,
            UNIDADE_MEDIDA: $quoteItem->measurement_unit,
            ITEM: $quoteItem->item,
            QUANTIDADE: $quoteItem->quantity,
            PRECO_UNITARIO: $quoteItem->unit_price,
            IPI: number_format($quoteItem->ipi, 2, ',', '.'),
            ICMS: number_format($quoteItem->icms, 2, ',', '.'),
            DATA_ENTREGA: null,
            ENTREGA_EM_DIAS: $quoteItem->delivery_in_days,
            INCLUIR_NA_COTACAO: $quoteItem->should_be_quoted,
            OBS: $quoteItem->comments,
            PRODUTO: ProtheusProductPayloadData::fromQuoteItem($quoteItem)
        );
    }
}

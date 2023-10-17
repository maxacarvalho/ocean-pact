<?php

namespace App\Data\Protheus\Quote\In;

use App\Data\Protheus\Quote\ProtheusProductPayloadData;
use App\Models\QuoteItem;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProtheusQuoteItemPayloadData extends Data
{
    public function __construct(
        public string $DESCRICAO,
        public string $UNIDADE_MEDIDA,
        public string $ITEM,
        public float $QUANTIDADE,
        public string $PRECO_UNITARIO,
        public string|Optional $IPI,
        public string|Optional $ICMS,
        public string|null $DATA_ENTREGA,
        public int|null|Optional $ENTREGA_EM_DIAS,
        public bool|null|Optional $INCLUIR_NA_COTACAO,
        public ?string $OBS,
        public ProtheusProductPayloadData $PRODUTO
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

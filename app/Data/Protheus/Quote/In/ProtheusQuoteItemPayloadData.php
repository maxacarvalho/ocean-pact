<?php

namespace App\Data\Protheus\Quote\In;

use App\Data\Protheus\Quote\ProtheusProductPayloadData;
use App\Models\QuoteItem;
use App\Utils\Money;
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
        public string $IPI,
        public string $ICMS,
        public string|null|Optional $DATA_ENTREGA,
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
            PRECO_UNITARIO: (string) Money::fromMinor($quoteItem->unit_price)->getBrickMoney()->getAmount(),
            IPI: (string) Money::fromMinor($quoteItem->ipi)->getBrickMoney()->getAmount(),
            ICMS: (string) Money::fromMinor($quoteItem->icms)->getBrickMoney()->getAmount(),
            DATA_ENTREGA: $quoteItem->delivery_date?->format('Y-m-d'),
            INCLUIR_NA_COTACAO: $quoteItem->should_be_quoted,
            OBS: $quoteItem->comments,
            PRODUTO: ProtheusProductPayloadData::fromQuoteItem($quoteItem)
        );
    }
}

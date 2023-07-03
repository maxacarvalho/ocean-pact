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
        public string|null $TIPO_FRETE,
        public string $VALOR_FRETE,
        public string $DESPESAS,
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
            PRECO_UNITARIO: Money::fromMinor($quoteItem->unit_price)->getBrickMoney()->getAmount(),
            IPI: Money::fromMinor($quoteItem->ipi)->getBrickMoney()->getAmount(),
            ICMS: Money::fromMinor($quoteItem->icms)->getBrickMoney()->getAmount(),
            TIPO_FRETE: $quoteItem->freight_type?->value,
            VALOR_FRETE: Money::fromMinor($quoteItem->freight_cost)->getBrickMoney()->getAmount(),
            DESPESAS: $quoteItem->expenses,
            DATA_ENTREGA: $quoteItem->delivery_date?->format('Y-m-d'),
            INCLUIR_NA_COTACAO: $quoteItem->should_be_quoted,
            OBS: $quoteItem->comments,
            PRODUTO: ProtheusProductPayloadData::fromQuoteItem($quoteItem)
        );
    }
}

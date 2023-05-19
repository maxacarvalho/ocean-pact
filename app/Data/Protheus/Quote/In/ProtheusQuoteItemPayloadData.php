<?php

namespace App\Data\Protheus\Quote\In;

use App\Data\Protheus\Quote\ProtheusProductPayloadData;
use App\Models\QuoteItem;
use Brick\Money\Money;
use NumberFormatter;
use Spatie\LaravelData\Data;

class ProtheusQuoteItemPayloadData extends Data
{
    public function __construct(
        public string $DESCRICAO,
        public string $UNIDADE_MEDIDA,
        public string $ITEM,
        public float $QUANTIDADE,
        public string $PRECO_UNITARIO,
        public ?string $OBS,
        public ProtheusProductPayloadData $PRODUTO
    ) {
    }

    public static function fromQuoteItem(QuoteItem $quote): self
    {
        return new self(
            DESCRICAO: $quote->description,
            UNIDADE_MEDIDA: $quote->measurement_unit,
            ITEM: $quote->item,
            QUANTIDADE: $quote->quantity,
            PRECO_UNITARIO: Money::ofMinor($quote->unit_price, 'BRL')->formatWith(self::getFormatter()),
            OBS: $quote->comments,
            PRODUTO: ProtheusProductPayloadData::fromQuoteItem($quote)
        );
    }

    private static function getFormatter(): NumberFormatter
    {
        $formatter = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
        $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, ',');
        $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '.');
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        return $formatter;
    }
}

<?php

namespace App\Data\QuotesPortal\Quote;

use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;

class ProtheusQuotePayloadData extends Data
{
    public function __construct(
        #[Required, Max(3)]
        public readonly string $EMPRESA,
        #[Required, Max(2)]
        public readonly string $FILIAL,
        #[Required]
        public readonly string $COTACAO,
        #[Required]
        public readonly string $SOLICITACAO_DE_COMPRAS,
        public readonly string|null|Optional $OBSERVACAO_GERAL,
        #[Required]
        public readonly ProtheusCurrencyPayloadData $MOEDAS,
        #[Required]
        public readonly ProtheusSupplierPayloadData $FORNECEDOR,
        #[Required]
        public readonly ProtheusPaymentConditionData $COND_PAGTO,
        #[Required]
        public readonly ProtheusBuyerPayloadData $COMPRADOR,
        #[DataCollectionOf(ProtheusQuoteItemPayloadData::class)]
        public readonly DataCollection $ITENS,
        #[DataCollectionOf(ProtheusSellerPayloadData::class)]
        public readonly DataCollection|Optional $VENDEDORES
    ) {
    }

    public static function fromQuote(Quote $quote): static
    {
        $quote->load([
            Quote::RELATION_ITEMS => [
                QuoteItem::RELATION_PRODUCT,
            ],
        ]);

        return new static(
            EMPRESA: $quote->company_code,
            FILIAL: $quote->company_code_branch,
            COTACAO: $quote->quote_number,
            SOLICITACAO_DE_COMPRAS: $quote->budget->budget_number,
            OBSERVACAO_GERAL: $quote->comments,
            MOEDAS: ProtheusCurrencyPayloadData::fromQuote($quote),
            FORNECEDOR: ProtheusSupplierPayloadData::fromQuote($quote),
            COND_PAGTO: ProtheusPaymentConditionData::fromQuote($quote),
            COMPRADOR: ProtheusBuyerPayloadData::fromQuote($quote),
            ITENS: ProtheusQuoteItemPayloadData::collection(
                $quote->items->map(fn (QuoteItem $item) => ProtheusQuoteItemPayloadData::fromQuoteItem($item))
            ),
            VENDEDORES: Optional::create()
        );
    }

    /**
     * @return ProtheusProductPayloadData[]
     */
    public function getProducts(): array
    {
        return $this->ITENS->map(fn (ProtheusQuoteItemPayloadData $item) => $item->PRODUTO)->all();
    }
}

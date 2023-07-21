<?php

namespace App\Data\Protheus\Quote\In;

use App\Data\Protheus\Quote\ProtheusBuyerPayloadData;
use App\Data\Protheus\Quote\ProtheusCurrencyPayloadData;
use App\Data\Protheus\Quote\ProtheusPaymentConditionData;
use App\Data\Protheus\Quote\ProtheusProductPayloadData;
use App\Data\Protheus\Quote\ProtheusSupplierPayloadData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;

class ProtheusQuotePayloadData extends Data
{
    public function __construct(
        public string $EMPRESA,
        public string $FILIAL,
        public string $SOLICITACAO_DE_COMPRAS,
        public string $COTACAO,
        public string|Optional|null $OBSERVACAO_GERAL,
        public ProtheusCurrencyPayloadData $MOEDAS,
        public ProtheusSupplierPayloadData $FORNECEDOR,
        public ProtheusPaymentConditionData $COND_PAGTO,
        public ProtheusBuyerPayloadData $COMPRADOR,
        #[DataCollectionOf(ProtheusQuoteItemPayloadData::class)]
        public DataCollection $ITENS
    ) {
    }

    /**
     * @return ProtheusProductPayloadData[]
     */
    public function getProducts(): array
    {
        return $this->ITENS->map(fn (ProtheusQuoteItemPayloadData $item) => $item->PRODUTO)->all();
    }
}

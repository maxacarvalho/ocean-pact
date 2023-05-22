<?php

namespace App\Data\Protheus\Quote\Out;

use App\Data\Protheus\Quote\In\ProtheusQuoteItemPayloadData;
use App\Data\Protheus\Quote\ProtheusBuyerPayloadData;
use App\Data\Protheus\Quote\ProtheusSupplierPayloadData;
use App\Models\Quote;
use App\Models\QuoteItem;
use DateTime;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
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
        #[WithCast(DateTimeInterfaceCast::class)]
        public DateTime|null $DATA_LIMITE_RESPOSTA,
        public string|null|Optional $OBSERVACAO_GERAL,
        public ProtheusSupplierPayloadData $FORNECEDOR,
        public string $COND_PAGTO,
        public ProtheusBuyerPayloadData $COMPRADOR,
        #[DataCollectionOf(ProtheusQuoteItemPayloadData::class)]
        public DataCollection $ITENS
    ) {
    }

    public static function fromQuote(Quote $quote): self
    {
        return new self(
            EMPRESA: $quote->company_code,
            FILIAL: $quote->company_code_branch,
            SOLICITACAO_DE_COMPRAS: $quote->budget->budget_number,
            COTACAO: $quote->quote_number,
            DATA_LIMITE_RESPOSTA: $quote->valid_until,
            OBSERVACAO_GERAL: $quote->comments,
            FORNECEDOR: ProtheusSupplierPayloadData::fromQuote($quote),
            COND_PAGTO: $quote->paymentCondition->code,
            COMPRADOR: ProtheusBuyerPayloadData::fromQuote($quote),
            ITENS: ProtheusQuoteItemPayloadData::collection(
                $quote->items->map(fn (QuoteItem $item) => ProtheusQuoteItemPayloadData::fromQuoteItem($item))
            )
        );
    }

    public function getHash(): string
    {
        return md5($this->toJson());
    }
}

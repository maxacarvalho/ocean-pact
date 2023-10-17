<?php

namespace App\Data\Protheus\Quote\Out;

use App\Data\Protheus\Quote\Out\ProtheusQuoteItemPayloadData;
use App\Data\Protheus\Quote\ProtheusBuyerPayloadData;
use App\Data\Protheus\Quote\ProtheusSupplierPayloadData;
use App\Enums\FreightTypeEnum;
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
        public ?DateTime $DATA_LIMITE_RESPOSTA,
        public string|null|Optional $OBSERVACAO_GERAL,
        public string $DESPESAS,
        public ?FreightTypeEnum $TIPO_FRETE,
        public string $VALOR_FRETE,
        public ?string $MODEDA,
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
            DATA_LIMITE_RESPOSTA: $quote->valid_until ?? $quote->updated_at,
            OBSERVACAO_GERAL: $quote->comments,
            DESPESAS: '0',
            TIPO_FRETE: $quote->freight_type,
            VALOR_FRETE: '0',
            MODEDA: null !== $quote->currency ? $quote->currency->protheus_acronym : null,
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

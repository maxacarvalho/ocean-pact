<?php

namespace App\Data\Protheus\Quote\Out;

use App\Data\Protheus\Quote\In\ProtheusQuoteItemPayloadData;
use App\Data\Protheus\Quote\ProtheusBuyerPayloadData;
use App\Data\Protheus\Quote\ProtheusSupplierPayloadData;
use DateTime;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;

class ProtheusQuotePayloadData extends Data
{
    public string $EMPRESA;
    public string $FILIAL;
    public string $SOLICITACAO_DE_COMPRAS;
    public string $COTACAO;
    #[WithCast(DateTimeInterfaceCast::class)]
    public DateTime $DATA_LIMITE_RESPOSTA;
    public string|null|Optional $OBSERVACAO_GERAL;
    public ProtheusSupplierPayloadData $FORNECEDOR;
    public string $COND_PAGTO;
    public ProtheusBuyerPayloadData $COMPRADOR;
    /** @var ProtheusQuoteItemPayloadData[] */
    public DataCollection $ITENS;
}

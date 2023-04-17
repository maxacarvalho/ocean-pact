<?php

namespace App\Data\Protheus\Quote\Out;

use App\Data\Protheus\Quote\ProtheusProductPayloadData;
use DateTime;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class ProtheusQuoteItemPayloadData extends Data
{
    public string $DESCRICAO;
    public string $UNIDADE_MEDIDA;
    public string $ITEM;
    public float $QUANTIDADE;
    public float $PRECO_UNITARIO;
    #[WithCast(DateTimeInterfaceCast::class)]
    public DateTime $DATA_ENTREGA;
    public bool $INCLUIR_NA_COTACAO;
    public string|null $OBS;
    public ProtheusProductPayloadData $PRODUTO;
}

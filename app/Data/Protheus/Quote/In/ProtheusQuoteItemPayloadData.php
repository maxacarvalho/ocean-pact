<?php

namespace App\Data\Protheus\Quote\In;

use App\Data\Protheus\Quote\ProtheusProductPayloadData;
use Spatie\LaravelData\Data;

class ProtheusQuoteItemPayloadData extends Data
{
    public string $DESCRICAO;
    public string $UNIDADE_MEDIDA;
    public string $ITEM;
    public float $QUANTIDADE;
    public float $PRECO_UNITARIO;
    public string|null $OBS;
    public ProtheusProductPayloadData $PRODUTO;
}

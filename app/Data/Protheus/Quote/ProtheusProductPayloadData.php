<?php

namespace App\Data\Protheus\Quote;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProtheusProductPayloadData extends Data
{
    public string $CODIGO;
    public string $DESCRICAO;
    public string $UNIDADE_MEDIDA;
    public string|null|Optional $FILIAL;
}

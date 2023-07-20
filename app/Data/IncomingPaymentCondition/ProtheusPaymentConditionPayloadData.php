<?php

namespace App\Data\IncomingPaymentCondition;

use Spatie\LaravelData\Data;

class ProtheusPaymentConditionPayloadData extends Data
{
    public string $EMPRESA;
    public ?string $FILIAL;
    public string $CONDICAO_PAGAMENTO;
    public string $DESCRICAO;
}

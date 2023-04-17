<?php

namespace App\Data\IncomingPaymentCondition;

use Spatie\LaravelData\Data;

class ProtheusPaymentConditionPayloadData extends Data
{
    public string $EMPRESA;
    public string|null $FILIAL;
    public string $CONDICAO_PAGAMENTO;
    public string $DESCRICAO;
}

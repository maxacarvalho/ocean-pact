<?php

namespace App\Data\Protheus\Quote;

use Spatie\LaravelData\Data;

class ProtheusCurrencyPayloadData extends Data
{
    public function __construct(
        public string $CODIGO,
        public string $MOEDA,
        public string $SIGLA,
        public string $EMPRESA,
        public string $DESCRICAO
    ) {
    }
}

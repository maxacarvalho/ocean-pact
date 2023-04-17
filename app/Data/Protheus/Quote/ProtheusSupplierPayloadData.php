<?php

namespace App\Data\Protheus\Quote;

use Spatie\LaravelData\Optional;

class ProtheusSupplierPayloadData extends \Spatie\LaravelData\Data
{
    public string $CODIGO;
    public string $LOJA;
    public string $NOME_FORNECEDOR;
    public string $NOME_FANTASIA;
    public string $UF;
    public string $EMAIL;
    public string $CONTATO;
    public string|null|Optional $FILIAL;
}

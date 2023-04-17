<?php

namespace App\Data\Protheus\Quote;

use Spatie\LaravelData\Optional;

class ProtheusBuyerPayloadData extends \Spatie\LaravelData\Data
{
    public string $CODIGO;
    public string $NOME;
    public string $EMAIL;
    public string|null|Optional $FILIAL;
}

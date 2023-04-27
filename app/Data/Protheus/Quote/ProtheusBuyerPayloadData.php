<?php

namespace App\Data\Protheus\Quote;

use App\Models\Company;
use App\Models\Quote;
use Spatie\LaravelData\Optional;

class ProtheusBuyerPayloadData extends \Spatie\LaravelData\Data
{
    public function __construct(
        public string $CODIGO,
        public string $NOME,
        public string $EMAIL,
        public string|null|Optional $FILIAL
    ) {
    }

    public static function fromQuote(Quote $quote): self
    {
        /** @var Company|null $company */
        $company = $quote->buyer->companies->count() ? $quote->buyer->companies->first() : null;

        return new self(
            CODIGO: $quote->buyer->buyer_code,
            NOME: $quote->buyer->name,
            EMAIL: $quote->buyer->email,
            FILIAL: $company?->code_branch
        );
    }
}

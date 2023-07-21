<?php

namespace App\Data\Protheus\Quote;

use App\Models\Company;
use App\Models\Quote;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProtheusBuyerPayloadData extends Data
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
        $company = $quote
            ->buyer
            ->companies()
            ->where(Company::CODE, '=', $quote->company_code)
            ->where(Company::CODE_BRANCH, '=', $quote->company_code_branch)
            ->first();

        return new self(
            CODIGO: $company->pivot->buyer_code,
            NOME: $quote->buyer->name,
            EMAIL: $quote->buyer->email,
            FILIAL: $company?->code_branch
        );
    }
}

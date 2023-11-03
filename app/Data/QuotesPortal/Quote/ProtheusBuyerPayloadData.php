<?php

namespace App\Data\QuotesPortal\Quote;

use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Quote;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProtheusBuyerPayloadData extends Data
{
    public function __construct(
        #[Required]
        public readonly string $CODIGO,
        #[Required]
        public readonly string $NOME,
        #[Required, Email]
        public readonly string $EMAIL,
        public readonly string|null|Optional $FILIAL
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

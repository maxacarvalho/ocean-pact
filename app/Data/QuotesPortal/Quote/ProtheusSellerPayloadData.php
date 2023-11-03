<?php

namespace App\Data\QuotesPortal\Quote;

use App\Models\User;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class ProtheusSellerPayloadData extends Data
{
    public function __construct(
        #[Required]
        public readonly string $NOME,
        #[Required, Email]
        public readonly string $EMAIL,
        #[Required]
        public readonly string $CODIGO,
        #[Required, BooleanType]
        public readonly bool $STATUS,
    ) {
        //
    }

    public static function fromUser(User $seller): static
    {
        return new static(
            NOME: $seller->name,
            EMAIL: $seller->email,
            CODIGO: $seller->id,
            STATUS: $seller->active,
        );
    }
}

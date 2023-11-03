<?php

namespace App\Data\IntegraHub;

use Spatie\LaravelData\Data;

class PayloadSuccessResponseData extends Data
{
    public function __construct(
        public readonly int $referenceId,
        public readonly array $details
    ) {
        //
    }
}

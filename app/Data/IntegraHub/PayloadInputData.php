<?php

namespace App\Data\IntegraHub;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class PayloadInputData extends Data
{
    public function __construct(
        public readonly array $payload,
        #[MapInputName('path_parameters')]
        public readonly ?array $pathParameters,
    ) {
        //
    }
}

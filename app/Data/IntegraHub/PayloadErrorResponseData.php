<?php

namespace App\Data\IntegraHub;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PayloadErrorResponseData extends Data
{
    public function __construct(
        public readonly int|Optional $referenceId,
        public readonly string $title,
        public readonly string|Optional $details,
        public readonly array $errors = [],
    ) {
        //
    }
}

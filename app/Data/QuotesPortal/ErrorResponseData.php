<?php

namespace App\Data\QuotesPortal;

use Spatie\LaravelData\Data;

class ErrorResponseData extends Data
{
    public function __construct(
        public string $title,
        public array $errors,
    ) {
        //
    }
}

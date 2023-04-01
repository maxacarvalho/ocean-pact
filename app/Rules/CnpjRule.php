<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CnpjRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ((new \App\Utils\Validators\Cnpj)->validate($value) === false) {
            $fail('validation.cnpj')->translate();
        }
    }
}

<?php

namespace App\Rules;

use App\Utils\Validators\Cnpj;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CnpjRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ((new Cnpj)->validate($value) === false) {
            $fail('validation.cnpj')->translate();
        }
    }
}

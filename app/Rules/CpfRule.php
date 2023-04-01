<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ((new \App\Utils\Validators\Cpf)->validate($value) === false) {
            $fail('validation.cpf')->translate();
        }
    }
}

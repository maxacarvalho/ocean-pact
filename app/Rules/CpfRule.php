<?php

namespace App\Rules;

use App\Utils\Validators\Cpf;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ((new Cpf)->validate($value) === false) {
            $fail('validation.cpf')->translate();
        }
    }
}

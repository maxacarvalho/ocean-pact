<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Cpf implements InvokableRule
{
    /**
     * Validates a Brazilian CPF.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function __invoke($attribute, $value, $fail): void
    {
        if (! $this->validate($value)) {
            $fail('validation.cpf')->translate();
        }
    }

    private function validate($input): bool
    {
        $c = preg_replace('/\D/', '', $input);

        if (mb_strlen($c) !== 11 || preg_match('/^'.$c[0].'{11}$/', $c) || $c === '01234567890') {
            return false;
        }

        $n = 0;
        for ($s = 10, $i = 0; $s >= 2; ++$i, --$s) {
            $n += $c[$i] * $s;
        }

        if ($c[9] !== (($n %= 11) < 2 ? 0 : 11 - $n)) {
            return false;
        }

        $n = 0;
        for ($s = 11, $i = 0; $s >= 2; ++$i, --$s) {
            $n += $c[$i] * $s;
        }

        $check = ($n %= 11) < 2 ? 0 : 11 - $n;

        return $c[10] === $check;
    }
}

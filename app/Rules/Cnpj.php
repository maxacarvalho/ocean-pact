<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Cnpj implements InvokableRule
{
    /**
     * Validates a Brazilian CNPJ.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail): void
    {
        if (! $this->validate($value)) {
            $fail('validation.cnpj')->translate();
        }
    }

    private function validate($input): bool
    {
        if (! is_scalar($input)) {
            return false;
        }

        // Code ported from jsfromhell.com
        $bases = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $digits = $this->getDigits((string) $input);

        if (array_sum($digits) < 1) {
            return false;
        }

        if (count($digits) !== 14) {
            return false;
        }

        $n = 0;
        for ($i = 0; $i < 12; $i++) {
            $n += $digits[$i] * $bases[$i + 1];
        }

        if ($digits[12] !== (($n %= 11) < 2 ? 0 : 11 - $n)) {
            return false;
        }

        $n = 0;
        for ($i = 0; $i <= 12; $i++) {
            $n += $digits[$i] * $bases[$i];
        }

        $check = ($n %= 11) < 2 ? 0 : 11 - $n;

        return $digits[13] === $check;
    }

    /**
     * @return int[]
     */
    private function getDigits(string $input): array
    {
        return array_map(
            'intval',
            str_split(
                (string) preg_replace('/\D/', '', $input)
            )
        );
    }
}

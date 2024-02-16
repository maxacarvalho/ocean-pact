<?php

namespace App\Rules;

use Closure;
use Cron\CronExpression;
use Illuminate\Contracts\Validation\ValidationRule;

class CronExpressionRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (CronExpression::isValidExpression($value) === false) {
            $fail('validation.cron_expression')->translate();
        }
    }
}

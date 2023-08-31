<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PercentageMaxValueRule implements ValidationRule
{
    protected float $size;

    public function __construct(float $size = 100)
    {
        $this->size = $size;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ((float) str_replace(',', '.', $value) > $this->size) {
            $fail('validation.max.numeric')->translate([
                'attribute' => $attribute,
                'max' => $this->size,
            ]);
        }
    }
}

<?php

namespace App\Utils;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ValidationRules
{
    public static function make(Collection $rules): array
    {
        return $rules
            ->map(function ($rawRules, $fieldName) {
                $rules = [];

                if (Arr::has($rawRules, 'sometimes')) {
                    unset($rawRules['sometimes']);
                    $rawRules = array_merge(['sometimes' => true], $rawRules);
                }

                if (Arr::has($rawRules, 'numeric')) {
                    unset($rawRules['numeric']);
                    $rawRules = array_merge(['numeric' => true], $rawRules);
                }

                if (Arr::has($rawRules, 'integer')) {
                    unset($rawRules['integer']);
                    $rawRules = array_merge(['integer' => true], $rawRules);
                }

                if (Arr::has($rawRules, 'boolean')) {
                    unset($rawRules['boolean']);
                    $rawRules = array_merge(['boolean' => true], $rawRules);
                }

                if (Arr::has($rawRules, 'string')) {
                    unset($rawRules['string']);
                    $rawRules = array_merge(['string' => true], $rawRules);
                }

                if (Arr::has($rawRules, 'required')) {
                    unset($rawRules['required']);
                    $rawRules = array_merge(['required' => true], $rawRules);
                }

                if (Arr::has($rawRules, 'custom')) {
                    $customRules = $rawRules['custom'];
                    unset($rawRules['custom']);

                    foreach ($customRules as $customRule) {
                        $rawRules[] = new $customRule;
                    }
                }

                if (is_array($rawRules)) {
                    foreach ($rawRules as $key => $value) {
                        if ($value === true) {
                            $rules[] = $key;
                        } elseif (is_a($value, ValidationRule::class)) {
                            $rules[] = $value;
                        } else {
                            $rules[] = "$key:$value";
                        }
                    }
                }

                return $rules;
            })
            ->toArray();
    }
}

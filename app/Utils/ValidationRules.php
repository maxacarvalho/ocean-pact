<?php

namespace App\Utils;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ValidationRules
{
    public static function make(Collection $rules): array
    {
        return $rules
            ->map(function ($rawRules, $fieldName) {
                $rules = [];

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

                foreach ($rawRules as $key => $value) {
                    if (true === $value) {
                        $rules[] = $key;
                    } else {
                        $rules[] = "$key:$value";
                    }
                }

                return implode('|', $rules);
            })
            ->toArray();
    }
}

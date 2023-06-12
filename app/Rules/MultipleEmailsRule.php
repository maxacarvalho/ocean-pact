<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class MultipleEmailsRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $emails = preg_split('/[,;\s-]+/', $value);

        foreach ($emails as $email) {
            $validator = Validator::make(['email' => $email], ['email' => 'email:rfc,dns']);
            if ($validator->fails()) {
                $fail('validation.multi_email')->translate();
            }
        }
    }
}

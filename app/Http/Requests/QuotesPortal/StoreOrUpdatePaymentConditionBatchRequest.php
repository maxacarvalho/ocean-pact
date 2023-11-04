<?php

namespace App\Http\Requests\QuotesPortal;

use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\PaymentCondition;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrUpdatePaymentConditionBatchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            '*.'.PaymentCondition::COMPANY_CODE => [
                'required',
                Rule::exists(Company::TABLE_NAME, Company::CODE),
                function (string $attribute, mixed $value, Closure $fail) {
                    $exists = Company::query()
                        ->where(Company::CODE, '=', $value)
                        ->when($this->input(PaymentCondition::COMPANY_CODE_BRANCH), function ($query, $codeBranch) {
                            $query->where(Company::CODE_BRANCH, '=', $codeBranch);
                        })
                        ->exists();

                    [$index] = explode('.', $attribute);

                    if (! $exists) {
                        $fail(__('company.validation_error_company_not_found', [
                            'code' => $value,
                            'code_branch' => $this->input($index.'.'.PaymentCondition::COMPANY_CODE_BRANCH),
                        ]));
                    }
                },
            ],
            '*.'.PaymentCondition::COMPANY_CODE_BRANCH => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, Closure $fail) {
                    $exists = Company::query()
                        ->where(Company::CODE, '=', $value)
                        ->when($this->input(PaymentCondition::COMPANY_CODE_BRANCH), function ($query, $codeBranch) {
                            $query->where(Company::CODE_BRANCH, '=', $codeBranch);
                        })
                        ->exists();

                    [$index] = explode('.', $attribute);

                    if (! $exists) {
                        $fail(__('company.validation_error_company_not_found', [
                            'code' => $this->input($index.'.'.PaymentCondition::COMPANY_CODE),
                            'code_branch' => $value,
                        ]));
                    }
                },
            ],
            '*.'.PaymentCondition::CODE => ['required', 'string'],
            '*.'.PaymentCondition::DESCRIPTION => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

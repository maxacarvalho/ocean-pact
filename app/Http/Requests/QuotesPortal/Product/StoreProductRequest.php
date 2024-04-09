<?php

namespace App\Http\Requests\QuotesPortal\Product;

use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Product;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            Product::COMPANY_CODE => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    $exists = Company::query()
                        ->where(Company::CODE, '=', $value)
                        ->where(Company::CODE_BRANCH, '=', $this->input(Product::COMPANY_CODE_BRANCH))
                        ->exists();

                    if (! $exists) {
                        $fail(__('company.validation_error_company_not_found', [
                            'code' => $value,
                            'code_branch' => $this->input(Product::COMPANY_CODE_BRANCH),
                        ]));
                    }
                },
            ],

            Product::COMPANY_CODE_BRANCH => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    $exists = Company::query()
                        ->where(Company::CODE_BRANCH, '=', $value)
                        ->where(Company::CODE, '=', $this->input(Product::COMPANY_CODE))
                        ->exists();

                    if (! $exists) {
                        $fail(__('company.validation_error_company_not_found', [
                            'code' => $this->input(Product::COMPANY_CODE),
                            'code_branch' => $value,
                        ]));
                    }
                },
            ],

            Product::CODE => [
                'required',
                Rule::unique(Product::TABLE_NAME, Product::CODE)
                    ->where(Product::COMPANY_CODE, $this->input(Product::COMPANY_CODE))
                    ->where(Product::COMPANY_CODE_BRANCH, $this->input(Product::COMPANY_CODE_BRANCH)),
            ],

            Product::DESCRIPTION => ['required'],

            Product::MEASUREMENT_UNIT => ['required'],

            Product::LAST_PRICE => ['required', 'numeric', 'min:0'],

            Product::SMALLEST_PRICE => ['required', 'numeric', 'min:0'],

            Product::SMALLEST_ETA => ['required', 'numeric', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

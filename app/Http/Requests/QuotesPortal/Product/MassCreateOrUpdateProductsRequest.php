<?php

namespace App\Http\Requests\QuotesPortal\Product;

use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Currency;
use App\Models\QuotesPortal\Product;
use App\Utils\Str;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MassCreateOrUpdateProductsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'products' => ['required', 'array'],
            'products.*.'.Product::COMPANY_CODE => Rule::forEach(function (string|null $value, string $attribute) {
                $companyCodeBranchAttribute = Str::replace(
                    Product::COMPANY_CODE,
                    Product::COMPANY_CODE_BRANCH,
                    $attribute
                );

                return [
                    'required',
                    function (string $attribute, mixed $value, Closure $fail) use ($companyCodeBranchAttribute) {
                        $exists = Company::query()
                            ->where(Company::CODE, '=', $value)
                            ->where(Company::CODE_BRANCH, '=', $this->input($companyCodeBranchAttribute))
                            ->exists();

                        if (! $exists) {
                            $fail(__('company.validation_error_company_not_found', [
                                'code' => $value,
                                'code_branch' => $companyCodeBranchAttribute,
                            ]));
                        }
                    },
                ];
            }),

            'products.*.'.Product::COMPANY_CODE_BRANCH => Rule::forEach(function (string|null $value, string $attribute) {
                $companyCodeAttribute = Str::replace(
                    Product::COMPANY_CODE_BRANCH,
                    Product::COMPANY_CODE,
                    $attribute
                );

                return [
                    'required',
                    function (string $attribute, mixed $value, Closure $fail) use ($companyCodeAttribute) {
                        $exists = Company::query()
                            ->where(Company::CODE_BRANCH, '=', $value)
                            ->where(Company::CODE, '=', $this->input($companyCodeAttribute))
                            ->exists();

                        if (! $exists) {
                            $fail(__('company.validation_error_company_not_found', [
                                'code' => $this->input($companyCodeAttribute),
                                'code_branch' => $value,
                            ]));
                        }
                    },
                ];
            }),

            'products.*.'.Product::CODE => Rule::forEach(function (string|null $value, string $attribute) {
                $companyCodeAttribute = Str::replace(
                    Product::COMPANY_CODE_BRANCH,
                    Product::COMPANY_CODE,
                    $attribute
                );

                $companyCodeBranchAttribute = Str::replace(
                    Product::COMPANY_CODE,
                    Product::COMPANY_CODE_BRANCH,
                    $attribute
                );

                return [
                    'required',
                    Rule::unique(Product::TABLE_NAME, Product::CODE)
                        ->where(Product::COMPANY_CODE, $this->input($companyCodeAttribute))
                        ->where(Product::COMPANY_CODE_BRANCH, $this->input($companyCodeBranchAttribute)),
                ];
            }),

            'products.*.'.Product::DESCRIPTION => ['sometimes', 'string'],

            'products.*.'.Product::MEASUREMENT_UNIT => ['sometimes', 'string'],

            'products.*.'.Product::LAST_PRICE => ['sometimes', 'array'],
            'products.*.'.Product::LAST_PRICE.'.currency' => [
                'sometimes',
                Rule::in(Currency::query()->pluck(Currency::ISO_CODE)->toArray()),
            ],
            'products.*.'.Product::LAST_PRICE.'.amount' => ['sometimes', 'numeric', 'min:0'],

            'products.*.'.Product::SMALLEST_PRICE => ['sometimes', 'array'],
            'products.*.'.Product::SMALLEST_PRICE.'.currency' => [
                'sometimes',
                Rule::in(Currency::query()->pluck(Currency::ISO_CODE)->toArray()),
            ],
            'products.*.'.Product::SMALLEST_PRICE.'.amount' => ['sometimes', 'numeric', 'min:0'],

            'products.*.'.Product::SMALLEST_ETA => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace App\Http\Requests\QuotesPortal\Product;

use App\Models\QuotesPortal\Currency;
use App\Models\QuotesPortal\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            Product::DESCRIPTION => ['sometimes', 'string'],

            Product::MEASUREMENT_UNIT => ['sometimes', 'string'],

            Product::LAST_PRICE => ['sometimes', 'array'],
            Product::LAST_PRICE.'.currency' => [
                'sometimes',
                Rule::in(Currency::query()->pluck(Currency::ISO_CODE)->toArray()),
            ],
            Product::LAST_PRICE.'.amount' => ['sometimes', 'numeric', 'min:0'],

            Product::SMALLEST_PRICE => ['sometimes', 'array'],
            Product::SMALLEST_PRICE.'.currency' => [
                'sometimes',
                Rule::in(Currency::query()->pluck(Currency::ISO_CODE)->toArray()),
            ],
            Product::SMALLEST_PRICE.'.amount' => ['sometimes', 'numeric', 'min:0'],

            Product::SMALLEST_ETA => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace App\Http\Requests\QuotesPortal;

use App\Models\QuotesPortal\PurchaseRequest;
use App\Models\QuotesPortal\Quote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            PurchaseRequest::QUOTE_ID => [
                'required',
                Rule::exists(Quote::TABLE_NAME, Quote::ID),
            ],
            PurchaseRequest::PURCHASE_REQUEST_NUMBER => [
                'required',
                'string',
            ],
            PurchaseRequest::FILE => [
                'required',
                'string',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

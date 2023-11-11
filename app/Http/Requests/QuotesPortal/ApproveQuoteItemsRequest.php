<?php

namespace App\Http\Requests\QuotesPortal;

use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApproveQuoteItemsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            Quote::RELATION_ITEMS => [
                'required',
                'array',
                Rule::exists(QuoteItem::TABLE_NAME, QuoteItem::ITEM)
                    ->where(QuoteItem::QUOTE_ID, $this->route('quote')),
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkQuoteAsAcceptedRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ITENS' => ['required', 'array'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

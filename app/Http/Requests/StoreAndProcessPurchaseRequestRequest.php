<?php

namespace App\Http\Requests;

use App\Models\Quote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAndProcessPurchaseRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ID_COTACAO' => [
                'required',
                Rule::exists(Quote::TABLE_NAME, Quote::ID),
            ],
            'NUMERO_PEDIDO' => [
                'required',
                'string',
            ],
            'ARQUIVO' => [
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

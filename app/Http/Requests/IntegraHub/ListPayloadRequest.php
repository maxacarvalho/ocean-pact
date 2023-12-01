<?php

namespace App\Http\Requests\IntegraHub;

use Illuminate\Foundation\Http\FormRequest;

class ListPayloadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'perPage' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

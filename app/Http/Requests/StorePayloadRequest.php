<?php

namespace App\Http\Requests;

use App\Models\IntegraHub\Payload;
use Illuminate\Foundation\Http\FormRequest;

class StorePayloadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            Payload::PAYLOAD => ['required', 'array'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\Payload;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayloadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            Payload::PAYLOAD => ['required', 'array'],
            IntegrationType::PATH_PARAMETERS => [
                'array',
                Rule::requiredIf(function () {
                    /** @var IntegrationType $integrationType */
                    $integrationType = $this->route('integration_type');

                    return is_array($integrationType->path_parameters) && count($integrationType->path_parameters) > 0;
                }),
            ],
        ];
    }
}

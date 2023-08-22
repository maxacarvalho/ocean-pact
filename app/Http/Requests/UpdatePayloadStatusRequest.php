<?php

namespace App\Http\Requests;

use App\Enums\PayloadProcessingAttemptsStatusEnum;
use App\Models\PayloadProcessingAttempt;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdatePayloadStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            PayloadProcessingAttempt::STATUS => [
                'required',
                'string',
                new Enum(PayloadProcessingAttemptsStatusEnum::class),
            ],
            PayloadProcessingAttempt::MESSAGE => [
                'required_if:'.PayloadProcessingAttempt::STATUS.','.PayloadProcessingAttemptsStatusEnum::FAILED->value,
                'string',
            ],
        ];
    }
}

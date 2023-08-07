<?php

namespace App\Http\Requests;

use App\Enums\PayloadProcessingAttemptsStatusEnum;
use App\Models\PayloadProcessingAttempt;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Spatie\Enum\Laravel\Http\Requests\TransformsEnums;

class UpdatePayloadStatusRequest extends FormRequest
{
    use TransformsEnums;

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

    public function enums(): array
    {
        return [
            PayloadProcessingAttempt::STATUS => PayloadProcessingAttemptsStatusEnum::class,
        ];
    }
}

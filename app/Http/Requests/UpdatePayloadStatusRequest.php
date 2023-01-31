<?php

namespace App\Http\Requests;

use App\Enums\PayloadProcessingAttemptsStatusEnum;
use App\Models\PayloadProcessingAttempt;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Enum\Laravel\Http\Requests\TransformsEnums;
use Spatie\Enum\Laravel\Rules\EnumRule;

class UpdatePayloadStatusRequest extends FormRequest
{
    use TransformsEnums;

    public function rules(): array
    {
        return [
            PayloadProcessingAttempt::STATUS => [
                'required',
                'string',
                new EnumRule(PayloadProcessingAttemptsStatusEnum::class),
            ],
            PayloadProcessingAttempt::MESSAGE => [
                'required_if:'.PayloadProcessingAttempt::STATUS.','.PayloadProcessingAttemptsStatusEnum::FAILED(),
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

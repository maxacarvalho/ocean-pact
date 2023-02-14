<?php

namespace App\Http\Controllers\API\Payload;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayloadRequest;
use App\Models\IntegrationType;
use App\Models\Payload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class StorePayloadController extends Controller
{
    public function __invoke(IntegrationType $integrationType, StorePayloadRequest $request): JsonResponse
    {
        $validationRules = $integrationType->fields()
            ->pluck('field_rules', 'field_name')
            ->map(function ($rawRules, $fieldName) {
                $rules = [];
                foreach ($rawRules as $key => $value) {
                    if (true === $value) {
                        $rules[] = $key;
                    } else {
                        $rules[] = "$key:$value";
                    }
                }

                return implode('|', $rules);
            })
            ->toArray();

        $payload = $request->validated(Payload::PAYLOAD);

        Validator::make($payload, $validationRules)->validate();

        /*$integrationType->payloads()->create(
            array_merge(
                $request->validated(),
                [
                    Payload::STORED_AT => now(),
                ],
            )
        );*/

        return response()->json([], Response::HTTP_CREATED);
    }
}

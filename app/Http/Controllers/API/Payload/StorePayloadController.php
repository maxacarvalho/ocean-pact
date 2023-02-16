<?php

namespace App\Http\Controllers\API\Payload;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayloadRequest;
use App\Models\IntegrationType;
use App\Models\Payload;
use App\Utils\ValidationRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class StorePayloadController extends Controller
{
    public function __invoke(IntegrationType $integrationType, StorePayloadRequest $request): JsonResponse
    {
        $validationRules = ValidationRules::make(
            $integrationType->fields()->pluck('field_rules', 'field_name')
        );

        $validationAttributes = [];
        foreach (array_keys($validationRules) as $fieldName) {
            $validationAttributes[$fieldName] = "`$fieldName`";
        }

        $payload = $request->validated(Payload::PAYLOAD);

        Validator::make($payload, $validationRules, attributes: $validationAttributes)->validate();

        $integrationType->payloads()->create(
            array_merge(
                $request->validated(),
                [
                    Payload::STORED_AT => now(),
                ],
            )
        );

        return response()->json([], Response::HTTP_CREATED);
    }
}

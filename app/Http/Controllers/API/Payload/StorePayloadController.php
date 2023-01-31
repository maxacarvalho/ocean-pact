<?php

namespace App\Http\Controllers\API\Payload;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayloadRequest;
use App\Models\IntegrationType;
use App\Models\Payload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StorePayloadController extends Controller
{
    public function __invoke(IntegrationType $integrationType, StorePayloadRequest $request): JsonResponse
    {
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

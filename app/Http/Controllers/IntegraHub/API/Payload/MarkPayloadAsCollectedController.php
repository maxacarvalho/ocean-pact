<?php

namespace App\Http\Controllers\IntegraHub\API\Payload;

use App\Enums\IntegraHub\PayloadProcessingStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\IntegraHub\Payload;
use App\Utils\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MarkPayloadAsCollectedController extends Controller
{
    public function __invoke(Payload $payload): JsonResponse
    {
        ray($payload->toArray());

        if ($payload->processing_status === PayloadProcessingStatusEnum::COLLECTED) {
            return response()->json([
                'errors' => [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'title' => Str::formatTitle(__('payload.payload_already_collected')),
                    'detail' => Str::formatTitle(__('payload.payload_already_collected_detail', ['payload_id' => $payload->id])),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $payload->update([
            Payload::PROCESSING_STATUS => PayloadProcessingStatusEnum::COLLECTED,
            Payload::PROCESSED_AT => now(),
        ]);

        return response()->json([], Response::HTTP_OK);
    }
}

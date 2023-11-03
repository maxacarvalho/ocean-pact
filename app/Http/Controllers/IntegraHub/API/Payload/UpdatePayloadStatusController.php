<?php

namespace App\Http\Controllers\IntegraHub\API\Payload;

use App\Enums\IntegraHub\PayloadProcessingAttemptsStatusEnum;
use App\Enums\IntegraHub\PayloadProcessingStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePayloadStatusRequest;
use App\Models\IntegraHub\Payload;
use App\Models\IntegraHub\PayloadProcessingAttempt;
use App\Utils\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UpdatePayloadStatusController extends Controller
{
    public function __invoke(Payload $payload, UpdatePayloadStatusRequest $request): JsonResponse
    {
        if ($payload->processing_status === PayloadProcessingStatusEnum::COLLECTED) {
            return response()->json([
                'errors' => [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'title' => Str::formatTitle(__('payload.payload_already_collected')),
                    'detail' => Str::formatTitle(__('payload.payload_already_collected_detail', ['payload_id' => $payload->id])),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var PayloadProcessingAttempt $processingAttempt */
        $processingAttempt = $payload->processingAttempts()->create($request->validated());

        $payload->update([
            Payload::PROCESSING_STATUS => $processingAttempt->status === PayloadProcessingAttemptsStatusEnum::SUCCESS
                ? PayloadProcessingStatusEnum::COLLECTED
                : PayloadProcessingStatusEnum::FAILED,
            Payload::PROCESSED_AT => now(),
        ]);

        return response()->json([], Response::HTTP_OK);
    }
}

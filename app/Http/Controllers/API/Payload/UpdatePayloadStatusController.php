<?php

namespace App\Http\Controllers\API\Payload;

use App\Enums\PayloadProcessingAttemptsStatusEnum;
use App\Enums\PayloadProcessingStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePayloadStatusRequest;
use App\Models\Payload;
use App\Models\PayloadProcessingAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UpdatePayloadStatusController extends Controller
{
    public function __invoke(Payload $payload, UpdatePayloadStatusRequest $request): JsonResponse
    {
        if ($payload->processing_status?->equals(PayloadProcessingStatusEnum::COLLECTED())) {
            return response()->json([
                'errors' => [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'title' => __('payload.PayloadAlreadyCollected'),
                    'detail' => __('payload.PayloadAlreadyCollectedDetail', ['payload_id' => $payload->id]),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var PayloadProcessingAttempt $processingAttempt */
        $processingAttempt = $payload->processingAttempts()->create($request->validated());

        $payload->update([
            Payload::PROCESSING_STATUS => $processingAttempt->status->equals(PayloadProcessingAttemptsStatusEnum::SUCCESS())
                ? PayloadProcessingStatusEnum::COLLECTED()
                : PayloadProcessingStatusEnum::FAILED(),
            Payload::PROCESSED_AT => now(),
        ]);

        return response()->json([], Response::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers\API\Payload;

use App\Enums\PayloadProcessingStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayloadRequest;
use App\Jobs\PayloadProcessors\ForwardPayloadProcessorJob;
use App\Models\IntegrationType;
use App\Models\Payload;
use App\Utils\Str;
use App\Utils\ValidationRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use JsonException;

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

        $payloadInput = $request->validated(Payload::PAYLOAD);

        Validator::make($payloadInput, $validationRules, attributes: $validationAttributes)->validate();

        if (! $integrationType->allows_duplicates) {
            try {
                $payloadHash = md5(json_encode($payloadInput, JSON_THROW_ON_ERROR));

                $duplicatedPayloadExists = $integrationType
                    ->payloads()
                    ->where(Payload::PROCESSING_STATUS, '!=', PayloadProcessingStatusEnum::FAILED)
                    ->where(Payload::PAYLOAD_HASH, '=', $payloadHash)
                    ->exists();

                if ($duplicatedPayloadExists) {
                    return response()->json([
                        'message' => Str::formatTitle(__('payload.payload_is_duplicated')),
                    ], Response::HTTP_CONFLICT);
                }
            } catch (JsonException $e) {
                Log::error('StorePayloadController: Unable to generate payloadInput hash', [
                    'payload' => $payloadInput,
                    'exception' => $e->getMessage(),
                ]);

                return response()->json([
                    'message' => 'Unable to process the given payloadInput',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        try {
            /** @var Payload $payloadModel */
            $payloadModel = $integrationType->payloads()->create([
                Payload::PAYLOAD => $payloadInput,
                Payload::PAYLOAD_HASH => md5(json_encode($payloadInput, JSON_THROW_ON_ERROR)),
                Payload::STORED_AT => now(),
                Payload::PROCESSING_STATUS => PayloadProcessingStatusEnum::READY,
            ]);

            if ($integrationType->isProcessable() && $integrationType->isSynchronous()) {
                return response()->json($payloadModel->dispatchToProcessor(), Response::HTTP_CREATED);
            }

            if ($integrationType->isProcessable()) {
                $payloadModel->dispatchToProcessor();
            }

            if ($integrationType->isForwardable()) {
                ForwardPayloadProcessorJob::dispatch($payloadModel->id);
            }
        } catch (JsonException $e) {
            Log::error('StorePayloadController: Unable to store payloadInput', [
                'payload' => $payloadInput,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to process the given payloadInput',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([], Response::HTTP_CREATED);
    }
}

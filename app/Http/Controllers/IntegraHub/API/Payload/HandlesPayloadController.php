<?php

namespace App\Http\Controllers\IntegraHub\API\Payload;

use App\Actions\IntegraHub\CreatePayloadAction;
use App\Actions\IntegraHub\HandlePayloadAction;
use App\Actions\IntegraHub\RecordFailedPayloadProcessingAttemptAction;
use App\Data\IntegraHub\PayloadData;
use App\Data\IntegraHub\PayloadErrorResponseData;
use App\Data\IntegraHub\PayloadInputData;
use App\Data\IntegraHub\PayloadSuccessResponseData;
use App\Enums\IntegraHub\PayloadProcessingStatusEnum;
use App\Exceptions\IntegraHub\DuplicatedPayloadException;
use App\Http\Controllers\Controller;
use App\Http\Requests\IntegraHub\StorePayloadRequest;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\Payload;
use App\Utils\Str;
use App\Utils\ValidationRules;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use JsonException;
use Throwable;

class HandlesPayloadController extends Controller
{
    public function __invoke(
        IntegrationType $integrationType,
        StorePayloadRequest $request,
        HandlePayloadAction $handlePayloadAction,
        CreatePayloadAction $createPayloadAction,
        RecordFailedPayloadProcessingAttemptAction $recordFailedPayloadProcessingAttemptAction
    ): PayloadSuccessResponseData|JsonResponse {
        $payloadInput = PayloadInputData::from($request->validated());

        $this->validatePayload($integrationType, $payloadInput->payload);

        $payload = null;

        try {
            if (! $integrationType->allows_duplicates) {
                $this->ensureItIsNotDuplicated($integrationType, $payloadInput->payload);
            }

            $payload = $createPayloadAction->handle(
                PayloadData::fromPayloadHandlerController($integrationType, $payloadInput)
            );

            $payload->markAsProcessing();

            return $handlePayloadAction->handle($integrationType, $payload, $payloadInput);
        } catch (DuplicatedPayloadException $e) {
            $responseError = PayloadErrorResponseData::from([
                'title' => $e->getMessage(),
            ]);

            return response()->json($responseError->toArray(), Response::HTTP_CONFLICT);
        } catch (RequestException $e) {
            Log::error('HandlesPayloadController: Unable to store payloadInput', [
                'exception_message' => $e->getMessage(),
                'context' => [
                    'payload_input' => $payloadInput,
                    'http_code' => $e->getCode(),
                    'http_response' => $e->response->json(),
                ],
            ]);

            if ($payload instanceof Payload) {
                $payload->markAsFailed($e->getMessage(), $e->response->json());
                $recordFailedPayloadProcessingAttemptAction->handle(
                    payloadId: $payload->id,
                    response: $e->response->json()
                );
            }

            $responseError = PayloadErrorResponseData::from([
                'title' => Str::ucfirst(__('general.http_response_error')),
                'details' => $e->getMessage(),
                'errors' => $e->response->json(),
            ]);

            return response()->json($responseError->toArray(), $e->getCode());
        } catch (Throwable $e) {
            Log::error('HandlesPayloadController: Unable to store payloadInput', [
                'exception_message' => $e->getMessage(),
                'context' => [
                    'payload_input' => $payloadInput,
                ],
            ]);

            if ($payload instanceof Payload) {
                $payload->markAsFailed($e->getMessage(), null);
                $recordFailedPayloadProcessingAttemptAction->handle(
                    payloadId: $payload->id,
                    response: [$e->getMessage()]
                );
            }

            $responseError = PayloadErrorResponseData::from([
                'title' => Str::ucfirst(__('general.unexpected_error')),
                'details' => $e->getMessage(),
            ]);

            return response()->json($responseError->toArray(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validatePayload(IntegrationType $integrationType, array $payloadInput): void
    {
        $validationRules = ValidationRules::make(
            $integrationType->fields()->pluck('field_rules', 'field_name')
        );

        $validationAttributes = [];
        foreach (array_keys($validationRules) as $fieldName) {
            $validationAttributes[$fieldName] = "`$fieldName`";
        }

        Validator::make($payloadInput, $validationRules, attributes: $validationAttributes)->validate();
    }

    /** @throws DuplicatedPayloadException|JsonException */
    private function ensureItIsNotDuplicated(IntegrationType $integrationType, array $payloadInput): void
    {
        $payloadHash = md5(json_encode($payloadInput, JSON_THROW_ON_ERROR));

        $duplicatedPayloadExists = $integrationType
            ->payloads()
            ->where(Payload::PROCESSING_STATUS, '!=', PayloadProcessingStatusEnum::FAILED)
            ->where(Payload::PAYLOAD_HASH, '=', $payloadHash)
            ->exists();

        if ($duplicatedPayloadExists) {
            throw new DuplicatedPayloadException();
        }
    }
}

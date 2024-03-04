<?php

namespace App\Services;

use App\Actions\IntegraHub\CreatePayloadAction;
use App\Actions\IntegraHub\HandlePayloadAction;
use App\Actions\IntegraHub\RecordFailedPayloadProcessingAttemptAction;
use App\Data\IntegraHub\PayloadData;
use App\Data\IntegraHub\PayloadInputData;
use App\Enums\IntegraHub\PayloadProcessingStatusEnum;
use App\Exceptions\IntegraHub\DuplicatedPayloadException;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\Payload;
use App\Utils\ValidationRules;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

readonly class PayloadService
{
    public function __construct(
        private HandlePayloadAction $handlePayloadAction,
        private CreatePayloadAction $createPayloadAction,
        private RecordFailedPayloadProcessingAttemptAction $recordFailedPayloadProcessingAttemptAction
    ) {
    }

    public function handlePayload(IntegrationType $integrationType, array $payloadInput): void
    {
        $payloadInput = PayloadInputData::from($payloadInput);

        $this->validatePathParameters($integrationType, $payloadInput->pathParameters);

        $this->validatePayload($integrationType, $payloadInput->payload);

        $payload = null;

        try {
            if (! $integrationType->allows_duplicates) {
                $this->ensureItIsNotDuplicated($integrationType, $payloadInput->payload);
            }

            $payload = $this->createPayloadAction->handle(
                PayloadData::fromPayloadHandlerController($integrationType, $payloadInput)
            );

            $payload->markAsProcessing();

            $this->handlePayloadAction->handle($integrationType, $payload, $payloadInput);
        } catch (DuplicatedPayloadException $e) {
            Log::error('PayloadService: Duplicate payload detected', [
                'exception_message' => $e->getMessage(),
                'context' => [
                    'payload_input' => $payloadInput,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('PayloadService: Unable to store payloadInput', [
                'exception_message' => $e->getMessage(),
                'context' => [
                    'payload_input' => $payloadInput,
                ],
            ]);

            if ($payload instanceof Payload) {
                $payload->markAsFailed($e->getMessage(), null);
                $this->recordFailedPayloadProcessingAttemptAction->handle(
                    payloadId: $payload->id,
                    response: [$e->getMessage()]
                );
            }
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

    private function validatePathParameters(IntegrationType $integrationType, ?array $pathParameters): void
    {
        if ($integrationType->path_parameters === null) {
            return;
        }

        $validationRules = collect($integrationType->path_parameters)
            ->mapWithKeys(fn (array $pathParameter) => [$pathParameter['parameter'] => 'required'])
            ->toArray();

        $validationAttributes = [];
        foreach (array_keys($validationRules) as $fieldName) {
            $validationAttributes[$fieldName] = "`$fieldName`";
        }

        Validator::make(
            data: $pathParameters ?? [],
            rules: $validationRules,
            attributes: $validationAttributes
        )->validate();
    }
}

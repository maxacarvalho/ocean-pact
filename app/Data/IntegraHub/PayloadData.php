<?php

namespace App\Data\IntegraHub;

use App\Enums\IntegraHub\PayloadProcessingStatusEnum;
use App\Enums\IntegraHub\PayloadStoringStatusEnum;
use App\Models\IntegraHub\IntegrationType;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class PayloadData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly int $integration_type_id,
        public readonly array $original_payload,
        public readonly array $payload,
        public readonly array|null $path_parameters,
        public readonly string|null $payload_hash,
        public readonly Carbon|null $stored_at,
        #[WithCast(EnumCast::class)]
        public readonly PayloadStoringStatusEnum|null $storing_status,
        public readonly Carbon|null $processed_at,
        #[WithCast(EnumCast::class)]
        public readonly PayloadProcessingStatusEnum|null $processing_status,
        public readonly array|null|Optional $response,
        public readonly string|null|Optional $error,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
    ) {
        //
    }

    public static function fromPayloadHandlerController(
        IntegrationType $integrationType,
        PayloadInputData $payloadInput
    ): PayloadData {
        $transformedPayload = self::transformPayload($integrationType, $payloadInput->payload);

        return PayloadData::from([
            'integration_type_id' => $integrationType->id,
            'original_payload' => $payloadInput->payload,
            'payload' => $transformedPayload,
            'path_parameters' => $payloadInput->pathParameters,
            'payload_hash' => md5(json_encode($transformedPayload, JSON_THROW_ON_ERROR)),
            'stored_at' => now(),
            'storing_status' => PayloadStoringStatusEnum::STORED,
            'processing_status' => PayloadProcessingStatusEnum::READY,
        ]);
    }

    public static function fromWebhookPayloadProcessor(IntegrationType $integrationType, array $payload): self
    {
        $transformedPayload = self::transformPayload($integrationType, $payload);

        return self::from([
            'integration_type_id' => $integrationType->id,
            'original_payload' => $payload,
            'payload' => $transformedPayload,
            'payload_hash' => md5(json_encode($transformedPayload)),
            'stored_at' => now(),
            'storing_status' => PayloadStoringStatusEnum::STORED,
            'processing_status' => PayloadProcessingStatusEnum::READY,
        ]);
    }

    private static function transformPayload(IntegrationType $integrationType, array $payload): array
    {
        $fields = $integrationType->fields;

        foreach ($fields as $field) {
            if (array_key_exists($field->field_name, $payload)) {
                $key = $field->alternate_name ?? $field->field_name;
                $value = $payload[$field->field_name];
                unset($payload[$field->field_name]);
                $payload[$key] = $value;
            }
        }

        return $payload;
    }
}

<?php

namespace App\Data\IntegraHub;

use App\Enums\IntegraHub\PayloadProcessingStatusEnum;
use App\Enums\IntegraHub\PayloadStoringStatusEnum;
use App\Models\IntegraHub\IntegrationType;
use Illuminate\Support\Arr;
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

    public static function transformPayload(IntegrationType $integrationType, array $payload): array
    {
        $mappingConfig = $integrationType->fields->pluck('alternate_name', 'field_name')->toArray();
        array_walk($mappingConfig, function (&$value, $key) {
            if (is_null($value)) {
                $value = $key;
            }
        });
        $regexMapping = self::createRegexMapping($mappingConfig);

        $dottedPayload = Arr::dot($payload);
        $transformedPayload = [];

        foreach ($dottedPayload as $key => $value) {
            $replacements = 0;
            foreach ($regexMapping as $pattern => $replacement) {
                $newKey = preg_replace('/'.$pattern.'/', $replacement, $key, -1, $replacements);
                if ($replacements) {
                    $transformedPayload[$newKey] = $value;
                    break;
                }
            }

            if ($replacements == 0) {
                $transformedPayload[$key] = $value;
            }
        }

        return Arr::undot($transformedPayload);
    }

    /**
     * Creates a regex mapping based on the mapping config by replacing
     * the wildcards with groups in the keys of the mapping and replacing
     * the wildcards in the values with references to the groups. E.g.:
     *
     * $mappingConfig = [
     *     'empresas.*.produtos.*.id' => 'companies.*.products.*.id'
     * ]
     *
     * returns [
     *     'empresas.(\d+).produtos.(\d+).id' => 'companies.$1.products.$2.id',
     * ]
     *
     * @param array<string, string> $mappingConfig
     * @return array<string, string>
     */
    private static function createRegexMapping(array $mappingConfig): array
    {
        $regexMapping = [];

        foreach ($mappingConfig as $key => $value) {
            $countMatches = 0;
            $newValue = preg_replace_callback('/\*/', function () use (&$countMatches) {
                return '$'.++$countMatches;
            }, $value);

            $newKey = str_replace('*', '(\d+)', $key);
            $regexMapping[$newKey] = $newValue;
        }

        return $regexMapping;
    }
}

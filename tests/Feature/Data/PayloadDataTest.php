<?php

use App\Data\IntegraHub\PayloadData;
use App\Data\IntegraHub\PayloadInputData;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\IntegrationTypeField;

describe('PayloadData', function () {
    test('should create PayloadData instance', function () {
        $integrationType = IntegrationType::factory()->create();

        $payload = [ 'message' => 'hello' ];
        $payloadInputData = PayloadInputData::from([
            'payload' => $payload,
        ]);

        $payloadData = PayloadData::fromPayloadHandlerController($integrationType, $payloadInputData);

        expect($payloadData)->toBeInstanceOf(PayloadData::class);
        expect($payloadData->integration_type_id)->toBe($integrationType->id);
        expect($payloadData->payload)->toBe($payload);
    });

    test('should transform payload data when integration_type fields alternate_name is set', function () {
        $integrationType = IntegrationType::factory()->create();
        IntegrationTypeField::factory()->create([
            IntegrationTypeField::INTEGRATION_TYPE_ID => $integrationType->id,
            IntegrationTypeField::FIELD_NAME => 'message',
            IntegrationTypeField::ALTERNATE_NAME => 'transformed',
        ]);

        $payload = [ 'message' => 'hello' ];
        $payloadInputData = PayloadInputData::from([
            'payload' => $payload,
        ]);

        $payloadData = PayloadData::fromPayloadHandlerController($integrationType, $payloadInputData);

        expect($payloadData->original_payload)->toBe($payload);
        expect($payloadData->payload)->toBe([
            'transformed' => 'hello',
        ]);
    });
});

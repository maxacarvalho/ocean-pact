<?php

use App\Data\IntegraHub\PayloadData;
use App\Data\IntegraHub\PayloadInputData;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\IntegrationTypeField;
use Illuminate\Support\Arr;

describe('PayloadData', function () {
    test('should create PayloadData instance', function () {
        $integrationType = IntegrationType::factory()->create();

        $payload = ['message' => 'hello'];
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

        $payload = ['message' => 'hello'];
        $payloadInputData = PayloadInputData::from([
            'payload' => $payload,
        ]);

        $payloadData = PayloadData::fromPayloadHandlerController($integrationType, $payloadInputData);

        expect($payloadData->original_payload)->toBe($payload);
        expect($payloadData->payload)->toBe([
            'transformed' => 'hello',
        ]);
    });

    test('should transform payload data', function () {
        $integrationType = IntegrationType::factory()->create();
        IntegrationTypeField::factory()->createMany([
            ['integration_type_id' => $integrationType->id, 'field_name' => '*.identificador', 'alternate_name' => '*.id'],
            ['integration_type_id' => $integrationType->id, 'field_name' => '*.nome_completo', 'alternate_name' => '*.name'],
            ['integration_type_id' => $integrationType->id, 'field_name' => '*.email', 'alternate_name' => '*.email'],
            ['integration_type_id' => $integrationType->id, 'field_name' => '*.empresa.razao_social', 'alternate_name' => '*.company.name'],
            ['integration_type_id' => $integrationType->id, 'field_name' => '*.empresa.endereco', 'alternate_name' => '*.company.address'],
            ['integration_type_id' => $integrationType->id, 'field_name' => '*.pedidos.*.SKU', 'alternate_name' => '*.orders.*.id'],
            ['integration_type_id' => $integrationType->id, 'field_name' => '*.pedidos.*.descricao', 'alternate_name' => '*.orders.*.product'],
            ['integration_type_id' => $integrationType->id, 'field_name' => '*.pedidos.*.qtd', 'alternate_name' => '*.orders.*.quantity'],
        ]);

        $payload = [
            [
                'identificador' => 1,
                'nome_completo' => 'John Doe',
                'email' => 'j@email.com',
                'empresa' => [
                    'razao_social' => 'ABC Company',
                    'endereco' => '123 Main St',
                ],
                'pedidos' => [
                    [
                        'SKU' => 1,
                        'descricao' => 'Shoes',
                        'qtd' => 2,
                    ],
                    [
                        'SKU' => 2,
                        'descricao' => 'Shirt',
                        'qtd' => 1,
                    ],
                ],
            ],
        ];

        $expectedResult = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'j@email.com',
                'company' => [
                    'name' => 'ABC Company',
                    'address' => '123 Main St',
                ],
                'orders' => [
                    [
                        'id' => 1,
                        'product' => 'Shoes',
                        'quantity' => 2,
                    ],
                    [
                        'id' => 2,
                        'product' => 'Shirt',
                        'quantity' => 1,
                    ],
                ],
            ],
        ];

        $transformedPayload = PayloadData::transformPayload($integrationType, $payload);
        $this->assertEquals($expectedResult, $transformedPayload);
    })->only();
});

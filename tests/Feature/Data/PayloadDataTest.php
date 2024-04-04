<?php

use App\Data\IntegraHub\PayloadData;
use App\Data\IntegraHub\PayloadInputData;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\IntegrationTypeField;

describe('PayloadData', function () {
    test('should create PayloadData instance', function () {
        $integrationType = IntegrationType::factory()->create();

        $payload = ['message' => 'hello'];
        $payloadInputData = PayloadInputData::from([
            'payload' => $payload,
        ]);

        $payloadData = PayloadData::fromPayloadHandlerController($integrationType, $payloadInputData);

        expect($payloadData)->toBeInstanceOf(PayloadData::class)
            ->and($payloadData->integration_type_id)->toBe($integrationType->id)
            ->and($payloadData->payload)->toBe($payload);
    });

    test('should transform payload data when target_integration_type_field is set', function () {
        $integrationType = IntegrationType::factory()->create();
        $targetField = IntegrationTypeField::factory()->create([
            IntegrationTypeField::INTEGRATION_TYPE_ID => IntegrationType::factory()->create()->id,
            IntegrationTypeField::FIELD_NAME => 'transformed',
        ]);
        IntegrationTypeField::factory()->create([
            IntegrationTypeField::INTEGRATION_TYPE_ID => $integrationType->id,
            IntegrationTypeField::FIELD_NAME => 'message',
            IntegrationTypeField::TARGET_INTEGRATION_TYPE_FIELD_ID => $targetField->id,
        ]);

        $payload = ['message' => 'hello'];
        $payloadInputData = PayloadInputData::from([
            'payload' => $payload,
        ]);

        $payloadData = PayloadData::fromPayloadHandlerController($integrationType, $payloadInputData);

        expect($payloadData->original_payload)->toBe($payload)
            ->and($payloadData->payload)->toBe([
                'transformed' => 'hello',
            ]);
    });

    test('should transform payload data', function () {
        $integrationType = IntegrationType::factory()->create();
        $targetIntegration = IntegrationType::factory()->create();
        $fieldsMap = [
            '*.identificador' => '*.id',
            '*.nome_completo' => '*.name',
            '*.email' => '*.email',
            '*.empresa.razao_social' => '*.company.name',
            '*.empresa.endereco' => '*.company.address',
            '*.pedidos.*.SKU' => '*.orders.*.id',
            '*.pedidos.*.descricao' => '*.orders.*.product',
            '*.pedidos.*.qtd' => '*.orders.*.quantity',
        ];

        foreach ($fieldsMap as $fieldName => $targetFieldName) {
            $targetField = IntegrationTypeField::factory()->create([
                IntegrationTypeField::INTEGRATION_TYPE_ID => $targetIntegration->id,
                IntegrationTypeField::FIELD_NAME => $targetFieldName,
            ]);

            IntegrationTypeField::factory()->create([
                IntegrationTypeField::INTEGRATION_TYPE_ID => $integrationType->id,
                IntegrationTypeField::FIELD_NAME => $fieldName,
                IntegrationTypeField::TARGET_INTEGRATION_TYPE_FIELD_ID => $targetField->id,
            ]);
        }

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

    test('should transform payload data with nested arrays inside nested arrays', function () {
        $integrationType = IntegrationType::factory()->create();

        $targetIntegration = IntegrationType::factory()->create();
        $fieldsMap = [
            'empresas.*.nome' => 'companies.*.name',
            'empresas.*.produtos.*.id' => 'companies.*.products.*.id',
            'empresas.*.produtos.*.nome' => 'companies.*.products.*.name',
        ];

        foreach ($fieldsMap as $fieldName => $targetFieldName) {
            $targetField = IntegrationTypeField::factory()->create([
                IntegrationTypeField::INTEGRATION_TYPE_ID => $targetIntegration->id,
                IntegrationTypeField::FIELD_NAME => $targetFieldName,
            ]);

            IntegrationTypeField::factory()->create([
                IntegrationTypeField::INTEGRATION_TYPE_ID => $integrationType->id,
                IntegrationTypeField::FIELD_NAME => $fieldName,
                IntegrationTypeField::TARGET_INTEGRATION_TYPE_FIELD_ID => $targetField->id,
            ]);
        }

        $payload = [
            'empresas' => [
                [
                    'nome' => 'ABC Company',
                    'produtos' => [
                        ['id' => 1, 'nome' => 'Shoes'],
                        ['id' => 2, 'nome' => 'Shirt'],
                    ],
                ],
                [
                    'nome' => 'XYZ Company',
                    'produtos' => [
                        ['id' => 3, 'nome' => 'Pants'],
                        ['id' => 4, 'nome' => 'Socks'],
                    ],
                ],
            ],
        ];

        $expectedResult = [
            'companies' => [
                [
                    'name' => 'ABC Company',
                    'products' => [
                        ['id' => 1, 'name' => 'Shoes'],
                        ['id' => 2, 'name' => 'Shirt'],
                    ],
                ],
                [
                    'name' => 'XYZ Company',
                    'products' => [
                        ['id' => 3, 'name' => 'Pants'],
                        ['id' => 4, 'name' => 'Socks'],
                    ],
                ],
            ],
        ];

        $transformedPayload = PayloadData::transformPayload($integrationType, $payload);
        $this->assertEquals($expectedResult, $transformedPayload);
    });

    test('should not transform payload data when target_integration_type_field is not set', function () {
        $integrationType = IntegrationType::factory()->create();
        $payload = ['message' => 'hello'];
        $transformedPayload = PayloadData::transformPayload($integrationType, $payload);
        $this->assertEquals($payload, $transformedPayload);
    });
});

<?php

use App\Enums\IntegraHub\IntegrationHandlingTypeEnum;
use App\Jobs\IntegraHub\CallExternalApiIntegrationJob;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\IntegrationTypeField;
use App\Services\PayloadService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

test('should call external api', function () {
    $integrationType = IntegrationType::factory()->create([
        IntegrationType::TARGET_URL => 'https://example.com',
    ]);

    $data = [
        'message' => 'Hello World',
    ];
    Http::fake([
        $integrationType->target_url => Http::response($data, 200),
    ]);

    $job = new CallExternalApiIntegrationJob($integrationType);
    $job->handle(App::make(PayloadService::class));

    Http::assertSent(function ($request) use ($integrationType) {
        return $request->url() === $integrationType->target_url;
    });

    $integrationType->refresh();
    $this->assertNotNull($integrationType->last_run_at);

    $this->assertDatabaseHas('payloads', [
        'integration_type_id' => $integrationType->id,
    ]);
    $payload = $integrationType->payloads()->first();
    $this->assertSame($data, $payload->payload);
});

test('should call external api and forward payload', function () {
    $integrationType = IntegrationType::factory()->create([
        IntegrationType::TARGET_URL => 'https://example.com',
        IntegrationType::FORWARD_URL => 'https://forward.example.com',
        IntegrationType::HANDLING_TYPE => IntegrationHandlingTypeEnum::FETCH_AND_SEND->value,
        IntegrationType::INTERVAL => 7200,
        IntegrationType::IS_SYNCHRONOUS => false,
    ]);
    IntegrationTypeField::factory()->create([
        IntegrationTypeField::INTEGRATION_TYPE_ID => $integrationType->id,
        IntegrationTypeField::FIELD_NAME => 'message',
        IntegrationTypeField::ALTERNATE_NAME => 'transformed_message',
    ]);

    $data = ['message' => 'Hello World'];
    $transformedData = ['transformed_message' => 'Hello World'];

    Http::fake([
        $integrationType->target_url => Http::response($data, 200),
        $integrationType->forward_url => Http::response(['success' => true], 200),
    ]);

    $job = new CallExternalApiIntegrationJob($integrationType);
    $job->handle(App::make(PayloadService::class));

    Http::assertSent(function ($request) use ($integrationType) {
        return $request->url() === $integrationType->target_url;
    });

    Http::assertSent(function ($request) use ($integrationType) {
        return $request->url() === $integrationType->forward_url;
    });

    $integrationType->refresh();
    $this->assertNotNull($integrationType->last_run_at);

    $this->assertDatabaseHas('payloads', [
        'integration_type_id' => $integrationType->id,
    ]);
    $payload = $integrationType->payloads()->first();
    $this->assertSame($data, $payload->original_payload);
    $this->assertSame($transformedData, $payload->payload);
    $this->assertDatabaseHas('payload_processing_attempts', [
        'payload_id' => $payload->id,
    ]);
});

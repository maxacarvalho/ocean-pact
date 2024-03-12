<?php

use App\Jobs\IntegraHub\CallExternalApiIntegrationJob;
use App\Models\IntegraHub\IntegrationType;
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

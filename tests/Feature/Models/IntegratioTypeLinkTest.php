<?php

use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\IntegrationTypeLink;

test('should be able to create an integration type link', function () {
    $integrationType = IntegrationType::factory()->create();
    $linkedIntegrationType = IntegrationType::factory()->create();

    $integrationTypeLink = new IntegrationTypeLink();
    $integrationTypeLink->integration_type_id = $integrationType->id;
    $integrationTypeLink->linked_integration_type_id = $linkedIntegrationType->id;

    expect($integrationTypeLink->save())->toBeTrue();

    $this->assertDatabaseHas('integration_type_links', [
        'integration_type_id' => $integrationType->id,
        'linked_integration_type_id' => $linkedIntegrationType->id,
    ]);

    $this->assertDatabaseCount('integration_type_links', 1);

    expect($integrationType->integrationTypeLinks()->first())->toBeInstanceOf(IntegrationTypeLink::class)
        ->and($integrationType->integrationTypeLinks()->first()->id)->toBe($integrationTypeLink->id);

    expect($integrationTypeLink->integrationType()->first()->id)->toBe($integrationType->id)
        ->and($integrationTypeLink->linkedIntegrationType()->first()->id)->toBe($linkedIntegrationType->id);

});

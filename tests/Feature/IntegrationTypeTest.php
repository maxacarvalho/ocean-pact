<?php

use App\Models\IntegraHub\IntegrationType;

test('Should add a new integration to the database', function () {
    $integrationType = IntegrationType::factory()->create();
    $this->assertDatabaseHas('integration_types', ['id' => $integrationType->id]);
});

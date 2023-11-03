<?php

namespace App\Jobs\PayloadProcessors;

use App\Models\IntegraHub\Payload;

interface PayloadProcessorInterface
{
    public function getPayload(): Payload;
}

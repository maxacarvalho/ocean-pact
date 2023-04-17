<?php

namespace App\Jobs\PayloadProcessors;

use App\Models\Payload;

interface PayloadProcessorInterface
{
    public function getPayload(): Payload;
}

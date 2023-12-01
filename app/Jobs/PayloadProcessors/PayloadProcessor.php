<?php

namespace App\Jobs\PayloadProcessors;

use App\Models\IntegraHub\Payload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class PayloadProcessor implements PayloadProcessorInterface, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Payload $payload;

    public function __construct(int $payloadId)
    {
        /** @var Payload $payload */
        $payload = Payload::query()->findOrFail($payloadId);

        $this->payload = $payload;
    }

    public function getPayload(): Payload
    {
        return $this->payload;
    }

    public function failed(Throwable $exception): void
    {
        $this->getPayload()->markAsFailed($exception->getMessage());

        Log::error('PayloadProcessor failed', [
            'payload_id' => $this->getPayload()->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}

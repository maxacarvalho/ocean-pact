<?php

namespace App\Jobs;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateQuoteRespondedPayloadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $quoteId
    ) {
    }

    public function handle(): void
    {
        $quote = Quote::query()->findOrFail($this->quoteId);

        if (! $quote->isResponded()) {
            $this->delete();
        }
    }
}

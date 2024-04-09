<?php

namespace App\Jobs\QuotesPortal;

use Cerbero\JsonParser\JsonParser;
use Cerbero\JsonParser\Tokens\Parser;
use Cerbero\JsonParser\Tokens\Token;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BatchMassCreateOrUpdateProductsPayloadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $filename,
    ) {
    }

    public function handle(): void
    {
        $json = JsonParser::parse(Storage::disk('local')->path($this->filename))->lazyPointer('/products');

        /** @var Token $value */
        foreach ($json as $key => $value) {
            $jobs = [];

            /** @var Parser $item */
            foreach ($value as $item) {
                $jobs[] = new MassCreateOrUpdateProductPayloadProcessorJob($item->toArray());

                if (count($jobs) === 1000) {
                    Bus::batch($jobs)
                        ->catch(function (Batch $batch, Throwable $exception) {
                            Log::error('BatchMassCreateOrUpdateProductsPayloadJob exception', [
                                'exception_message' => $exception->getMessage(),
                            ]);
                        })
                        ->dispatch();

                    $jobs = [];
                }
            }

            if (count($jobs) > 0) {
                Bus::batch($jobs)
                    ->then(function () {
                        Storage::disk('local')->delete($this->filename);
                    })
                    ->catch(function (Batch $batch, Throwable $exception) {
                        Log::error('BatchMassCreateOrUpdateProductsPayloadJob exception', [
                            'exception_message' => $exception->getMessage(),
                        ]);
                    })
                    ->dispatch();
            }
        }
    }
}

<?php

namespace App\Jobs;

use App\Data\PurchaseRequestRequestData;
use App\Filament\Resources\PurchaseRequestResource;
use App\Mail\PurchaseRequestGeneratedMail;
use App\Models\PurchaseRequest;
use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PurchaseRequestReceivedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public PurchaseRequestRequestData $purchaseRequestData
    ) {
        //
    }

    public function handle(): void
    {
        $purchaseRequest = $this->createPurchaseRequest();

        $quote = $purchaseRequest->quote;
        $supplier = $purchaseRequest->quote->supplier;

        $addresses = collect(explode(';', $supplier->email))->map(function ($email) {
            return trim($email);
        })->toArray();

        $url = PurchaseRequestResource::getUrl();

        foreach ($addresses as $address) {
            if (! $this->isEmailValid($address)) {
                Log::info('Invalid email address', [
                    'address' => $address,
                    'supplier' => $supplier->id,
                    'quote' => $quote->id,
                ]);

                continue;
            }

            Mail::to($address)->send(
                new PurchaseRequestGeneratedMail(
                    supplier_name: $supplier->name,
                    company_business_name: $quote->company->business_name,
                    purchase_request_number: $purchaseRequest->purchase_request_number,
                    url: $url
                )
            );
        }
    }

    private function isEmailValid(string $email): bool
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ]);

        return $validator->passes();
    }

    private function createPurchaseRequest(): PurchaseRequest
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::query()->create(
            array_merge(
                $this->purchaseRequestData->toArray(),
                [
                    PurchaseRequest::SENT_AT => now(),
                ]
            )
        );

        $purchaseRequest->load(PurchaseRequest::RELATION_QUOTE);
        $purchaseRequest->load(PurchaseRequest::RELATION_QUOTE.'.'.Quote::RELATION_COMPANY);
        $purchaseRequest->load(PurchaseRequest::RELATION_QUOTE.'.'.Quote::RELATION_SUPPLIER);

        return $purchaseRequest;
    }
}

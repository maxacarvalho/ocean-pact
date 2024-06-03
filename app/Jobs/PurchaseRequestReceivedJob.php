<?php

namespace App\Jobs;

use App\Enums\QuotesPortal\PurchaseRequestStatus;
use App\Filament\Resources\PurchaseRequestResource;
use App\Mail\QuotePortal\PurchaseRequestGeneratedMail;
use App\Models\QuotesPortal\PurchaseRequest;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class PurchaseRequestReceivedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $purchaseRequestId
    ) {
        //
    }

    public function handle(): void
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = PurchaseRequest::query()->findOrFail($this->purchaseRequestId);

        if ($purchaseRequest->status !== PurchaseRequestStatus::APPROVED) {
            $this->delete();

            return;
        }

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $purchaseRequest->load([
            PurchaseRequest::RELATION_QUOTE => [
                Quote::RELATION_SUPPLIER => [
                    Supplier::RELATION_SELLERS,
                ],
            ],
        ]);

        $quote = $purchaseRequest->quote;
        $supplier = $purchaseRequest->quote->supplier;

        $url = PurchaseRequestResource::getUrl();

        foreach ($supplier->sellers as $seller) {
            if (!$seller->isActive()) {
                continue;
            }

            Mail::to($seller)->send(
                new PurchaseRequestGeneratedMail(
                    supplier_name: $supplier->name,
                    company_business_name: $quote->company->business_name,
                    purchase_request_number: $purchaseRequest->purchase_request_number,
                    url: $url
                )
            );
        }

        $purchaseRequest->update([
            PurchaseRequest::SENT_AT => now(),
        ]);
    }
}

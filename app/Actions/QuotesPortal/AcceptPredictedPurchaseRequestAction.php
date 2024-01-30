<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\FinalizedPredictedPurchaseRequestData;
use App\Enums\QuotesPortal\PredictedPurchaseRequestStatusEnum;
use App\Exceptions\QuotesPortal\MissingPredictedPurchaseRequestItemsException;
use App\Exceptions\QuotesPortal\PredictedPurchaseRequestAlreadyAcceptedException;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\PredictedPurchaseRequest;
use App\Models\QuotesPortal\Product;
use App\Models\QuotesPortal\QuoteItem;
use App\Models\QuotesPortal\Supplier;
use App\Models\User;
use App\Utils\Str;
use Illuminate\Database\Eloquent\Collection;
use Spatie\WebhookServer\WebhookCall;

class AcceptPredictedPurchaseRequestAction
{
    /** @throws MissingPredictedPurchaseRequestItemsException|PredictedPurchaseRequestAlreadyAcceptedException */
    public function handle(int $companyId, string $quoteNumber): void
    {
        /** @var PredictedPurchaseRequest[]|Collection $purchaseRequestItems */
        $purchaseRequestItems = PredictedPurchaseRequest::query()
            ->with([
                PredictedPurchaseRequest::RELATION_COMPANY,
                PredictedPurchaseRequest::RELATION_QUOTE,
                PredictedPurchaseRequest::RELATION_BUYER => [
                    User::RELATION_COMPANIES,
                ],
                PredictedPurchaseRequest::RELATION_SUPPLIER,
                PredictedPurchaseRequest::RELATION_QUOTE_ITEM => [
                    QuoteItem::RELATION_PRODUCT,
                ],
            ])
            ->where(PredictedPurchaseRequest::COMPANY_ID, $companyId)
            ->where(PredictedPurchaseRequest::QUOTE_NUMBER, $quoteNumber)
            ->orderBy(PredictedPurchaseRequest::SUPPLIER_ID)
            ->orderBy(PredictedPurchaseRequest::ITEM)
            ->get();

        if ($purchaseRequestItems->isEmpty()) {
            throw new MissingPredictedPurchaseRequestItemsException(
                Str::ucfirst(__('quote_analysis_panel.missing_predicted_purchase_request_items'))
            );
        }

        if ($purchaseRequestItems->contains(PredictedPurchaseRequest::STATUS, PredictedPurchaseRequestStatusEnum::ACCEPTED)) {
            throw new PredictedPurchaseRequestAlreadyAcceptedException(
                Str::ucfirst(__('quote_analysis_panel.predicted_purchase_request_already_accepted'))
            );
        }

        $suppliers = [];

        /** @var PredictedPurchaseRequest $purchaseRequestItem */
        foreach ($purchaseRequestItems->unique(PredictedPurchaseRequest::SUPPLIER_ID) as $purchaseRequestItem) {
            $suppliers[$purchaseRequestItem->supplier_id] = [
                Supplier::ID => $purchaseRequestItem->supplier_id,
                Supplier::CODE => $purchaseRequestItem->supplier->code,
                Supplier::STORE => $purchaseRequestItem->supplier->store,
                Supplier::COMPANY_CODE => $purchaseRequestItem->supplier->company_code,
                Supplier::COMPANY_CODE_BRANCH => $purchaseRequestItem->supplier->company_code_branch,
                Supplier::CNPJ_CPF => $purchaseRequestItem->supplier->cnpj_cpf,
                Supplier::NAME => $purchaseRequestItem->supplier->name,
                Supplier::BUSINESS_NAME => $purchaseRequestItem->supplier->business_name,
                'items' => $purchaseRequestItems
                    ->where(PredictedPurchaseRequest::SUPPLIER_ID, $purchaseRequestItem->supplier_id)
                    ->map(function (PredictedPurchaseRequest $purchaseRequestItem) {
                        $quoteItem = $purchaseRequestItem->quoteItem;

                        return [
                            QuoteItem::ITEM => $quoteItem->item,
                            QuoteItem::IPI => $quoteItem->ipi,
                            QuoteItem::ICMS => $quoteItem->icms,
                            QuoteItem::COMMENTS => $quoteItem->comments,
                            QuoteItem::QUANTITY => $quoteItem->quantity,
                            QuoteItem::UNIT_PRICE => [
                                'currency' => $quoteItem->unit_price->getCurrency()->getCurrencyCode(),
                                'amount' => $quoteItem->unit_price->getMinorAmount()->toInt(),
                            ],
                            QuoteItem::DESCRIPTION => $quoteItem->description,
                            QuoteItem::DELIVERY_IN_DAYS => $quoteItem->delivery_in_days,
                            QuoteItem::SHOULD_BE_QUOTED => $quoteItem->should_be_quoted,
                            QuoteItem::RELATION_PRODUCT => [
                                Product::CODE => $quoteItem->product->code,
                                Product::DESCRIPTION => $quoteItem->product->description,
                            ],
                        ];
                    })->values()->toArray(),
            ];
        }

        /** @var PredictedPurchaseRequest $first */
        $first = $purchaseRequestItems->first();

        $data = FinalizedPredictedPurchaseRequestData::from([
            PredictedPurchaseRequest::QUOTE_NUMBER => $quoteNumber,
            PredictedPurchaseRequest::RELATION_COMPANY => $first->company,
            PredictedPurchaseRequest::RELATION_BUYER => [
                User::ID => $first->buyer->id,
                User::NAME => $first->buyer->name,
                User::EMAIL => $first->buyer->email,
                'buyer_company' => $first->buyer->companies
                    ->where(Company::ID, $first->company_id)
                    ->first()?->buyer_company,
            ],
            'suppliers' => array_values($suppliers),
        ]);

        PredictedPurchaseRequest::query()
            ->where(PredictedPurchaseRequest::COMPANY_ID, $companyId)
            ->where(PredictedPurchaseRequest::QUOTE_NUMBER, $quoteNumber)
            ->update([
                PredictedPurchaseRequest::STATUS => PredictedPurchaseRequestStatusEnum::ACCEPTED,
            ]);

        WebhookCall::create()
            ->url(config('integra-hub.base_url').'/integra-hub/webhooks/payload?integration-type-code=analise-cotacao-finalizada')
            ->payload($data->toArray())
            ->useSecret(config('integra-hub.webhook-secret'))
            ->dispatchSync();
    }
}

<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\FinalizedPredictedPurchaseRequestData;
use App\Enums\QuotesPortal\PredictedPurchaseRequestStatusEnum;
use App\Enums\QuotesPortal\QuoteItemStatusEnum;
use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Exceptions\QuotesPortal\MissingPredictedPurchaseRequestItemsException;
use App\Exceptions\QuotesPortal\PredictedPurchaseRequestAlreadyAcceptedException;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\PredictedPurchaseRequest;
use App\Models\QuotesPortal\Product;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteAnalysisAction;
use App\Models\QuotesPortal\QuoteItem;
use App\Models\QuotesPortal\Supplier;
use App\Models\User;
use App\Utils\Str;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Spatie\WebhookServer\WebhookCall;

class AcceptPredictedPurchaseRequestAction
{
    /** @throws MissingPredictedPurchaseRequestItemsException|PredictedPurchaseRequestAlreadyAcceptedException */
    public function handle(int $companyId, string $quoteNumber): void
    {
        $purchaseRequestItems = $this->getPredictedPurchaseRequestItems($companyId, $quoteNumber);

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

        $suppliers = $this->getSuppliersFromPredictedPurchaseRequest($purchaseRequestItems);

        /** @var PredictedPurchaseRequest $first */
        $first = $purchaseRequestItems->first();

        $data = $this->getFinalizedPredictedPurchaseRequestData($quoteNumber, $first, $suppliers);

        $this->markPredictedPurchaseRequestAsAccepted($companyId, $quoteNumber);

        $purchaseRequestQuoteIds = $purchaseRequestItems->pluck(PredictedPurchaseRequest::QUOTE_ID)->toArray();
        $purchaseRequestQuoteItemsIds = $purchaseRequestItems->pluck(PredictedPurchaseRequest::QUOTE_ITEM_ID)->toArray();

        $this->markAllRelatedQuotesAsAnalyzed($companyId, $quoteNumber);

        $this->markPurchaseRequestQuoteItemsAsAccepted($purchaseRequestQuoteItemsIds);

        $this->markAllQuoteItemsNotIncludedInTheAcceptedPurchaseRequestAsRejected($companyId, $quoteNumber, $purchaseRequestQuoteItemsIds);

        WebhookCall::create()
            ->url(config('integra-hub.base_url').'/integra-hub/webhooks/payload?integration-type-code=analise-cotacao-finalizada')
            ->payload($data->toArray())
            ->useSecret(config('integra-hub.webhook-secret'))
            ->dispatchSync();
    }

    /**
     * @return PredictedPurchaseRequest[]|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    private function getPredictedPurchaseRequestItems(int $companyId, string $quoteNumber): array|Collection
    {
        return PredictedPurchaseRequest::query()
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
    }

    private function getSuppliersFromPredictedPurchaseRequest(Collection|array $purchaseRequestItems): array
    {
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

        return $suppliers;
    }

    private function getFinalizedPredictedPurchaseRequestData(
        string $quoteNumber,
        PredictedPurchaseRequest $first,
        array $suppliers
    ): FinalizedPredictedPurchaseRequestData {
        return FinalizedPredictedPurchaseRequestData::from([
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
            'actions' => QuoteAnalysisAction::query()
                ->with([
                    QuoteAnalysisAction::RELATION_QUOTE => [
                        Quote::RELATION_SUPPLIER,
                        Quote::RELATION_ITEMS,
                    ],
                ])
                ->where(QuoteAnalysisAction::QUOTE_NUMBER, $quoteNumber)->get()->toArray(),
        ]);
    }

    private function markPredictedPurchaseRequestAsAccepted(int $companyId, string $quoteNumber): void
    {
        PredictedPurchaseRequest::query()
            ->where(PredictedPurchaseRequest::COMPANY_ID, $companyId)
            ->where(PredictedPurchaseRequest::QUOTE_NUMBER, $quoteNumber)
            ->update([
                PredictedPurchaseRequest::STATUS => PredictedPurchaseRequestStatusEnum::ACCEPTED,
            ]);
    }

    private function markAllRelatedQuotesAsAnalyzed(int $companyId, string $quoteNumber): void
    {
        Quote::query()
            ->whereIn(Quote::COMPANY_ID, $this->getAllRelatedQuotes($companyId, $quoteNumber))
            ->update([
                Quote::STATUS => QuoteStatusEnum::ANALYZED,
            ]);
    }

    private function markPurchaseRequestQuoteItemsAsAccepted(array $purchaseRequestQuoteItemsIds): void
    {
        QuoteItem::query()
            ->whereIn(QuoteItem::ID, $purchaseRequestQuoteItemsIds)
            ->update([
                QuoteItem::STATUS => QuoteItemStatusEnum::ACCEPTED,
            ]);
    }

    private function getAllRelatedQuotes(int $companyId, string $quoteNumber): array
    {
        return Quote::query()
            ->where(Quote::COMPANY_ID, '=', $companyId)
            ->where(Quote::QUOTE_NUMBER, '=', $quoteNumber)
            ->pluck(Quote::ID)
            ->toArray();
    }

    private function markAllQuoteItemsNotIncludedInTheAcceptedPurchaseRequestAsRejected(
        int $companyId,
        string $quoteNumber,
        array $purchaseRequestQuoteItemsIds
    ): void {
        QuoteItem::query()
            ->where(function (Builder $query) use ($companyId, $quoteNumber, $purchaseRequestQuoteItemsIds) {
                $query
                    ->whereIn(QuoteItem::QUOTE_ID, $this->getAllRelatedQuotes($companyId, $quoteNumber))
                    ->whereNotIn(QuoteItem::ID, $purchaseRequestQuoteItemsIds);
            })
            ->update([
                QuoteItem::STATUS => QuoteItemStatusEnum::REJECTED,
            ]);
    }
}

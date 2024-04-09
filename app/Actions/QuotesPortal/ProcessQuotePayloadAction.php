<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\StoreQuotePayloadData;
use App\Models\QuotesPortal\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class ProcessQuotePayloadAction
{
    public function __construct(
        private FindOrCreateBuyerAction $findOrCreateBuyerAction,
        private FindOrCreateSuppliersAction $findOrCreateSuppliersAction,
        private FindOrCreateBudgetAction $findOrCreateBudgetAction,
        private FindOrCreateCurrencyAction $findOrCreateCurrencyAction,
        private FindOrCreatePaymentConditionAction $findOrCreatePaymentConditionAction,
        private FindOrCreateProductsAction $findOrCreateProductsAction,
        private CreateQuotesAction $createQuotesAction
    ) {
        //
    }

    public function handle(StoreQuotePayloadData $quotePayloadData): array
    {
        /** @var Company $company */
        $company = Company::query()
            ->where(Company::CODE, '=', $quotePayloadData->companyCode)
            ->where(Company::CODE_BRANCH, '=', $quotePayloadData->companyCodeBranch)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $buyer = $this->findOrCreateBuyerAction->handle($quotePayloadData, $company);
            $mappingCodesToProductsIds = $this->findOrCreateProductsAction->handle($quotePayloadData);
            $mappingCodesAndStoresToSuppliersIds = $this->findOrCreateSuppliersAction->handle($quotePayloadData, $company);
            $budget = $this->findOrCreateBudgetAction->handle($quotePayloadData);
            $currency = $this->findOrCreateCurrencyAction->handle($quotePayloadData);
            $paymentCondition = $this->findOrCreatePaymentConditionAction->handle($quotePayloadData);

            $quotesIds = $this->createQuotesAction->handle(
                $quotePayloadData,
                $company,
                $paymentCondition->id,
                $buyer->id,
                $budget->id,
                $currency->id,
                $mappingCodesAndStoresToSuppliersIds,
                $mappingCodesToProductsIds
            );

            DB::commit();

            return $quotesIds;
        } catch (Throwable $exception) {
            Log::error('ProcessQuotePayloadAction unexpected exception', [
                'namespace' => __CLASS__,
                'exception_message' => $exception->getMessage(),
                'context' => [
                    'quote_payload_data' => $quotePayloadData->toArray(),
                ],
            ]);

            DB::rollBack();

            throw $exception;
        }
    }
}

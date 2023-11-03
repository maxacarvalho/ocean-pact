<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\Quote\ProtheusQuotePayloadData;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProcessQuotePayloadAction
{
    public function __construct(
        private readonly FindOrCreateBuyerAction $findOrCreateBuyerAction,
        private readonly FindOrCreateSupplierAction $findOrCreateSupplierAction,
        private readonly FindOrCreateBudgetAction $findOrCreateBudgetAction,
        private readonly FindOrCreateCurrencyAction $findOrCreateCurrencyAction,
        private readonly FindOrCreatePaymentConditionAction $findOrCreatePaymentConditionAction,
        private readonly FindOrCreateProductsAction $findOrCreateProductsAction,
        private readonly CreateQuoteAction $createQuoteAction,
        private readonly CreateSellerAction $createSellerAction
    ) {
        //
    }

    public function handle(ProtheusQuotePayloadData $quotePayloadData): Quote
    {
        $company = $this->getCompany($quotePayloadData);

        $buyer = $this->findOrCreateBuyerAction->handle($quotePayloadData, $company);

        $supplier = $this->findOrCreateSupplierAction->handle($quotePayloadData, $company);
        foreach ($quotePayloadData->VENDEDORES as $seller) {
            $this->createSellerAction->handle($seller, $supplier);
        }

        $budget = $this->findOrCreateBudgetAction->handle($quotePayloadData);
        $currency = $this->findOrCreateCurrencyAction->handle($quotePayloadData);
        $paymentCondition = $this->findOrCreatePaymentConditionAction->handle($quotePayloadData);
        $codeToProductsMapping = $this->findOrCreateProductsAction->handle($quotePayloadData);

        $quote = $this->createQuoteAction->handle($budget, $currency, $supplier, $paymentCondition, $buyer, $quotePayloadData);

        foreach ($quotePayloadData->ITENS as $item) {
            $quote->items()->create([
                QuoteItem::PRODUCT_ID => $codeToProductsMapping[$item->PRODUTO->CODIGO],
                QuoteItem::DESCRIPTION => $item->DESCRICAO,
                QuoteItem::MEASUREMENT_UNIT => $item->UNIDADE_MEDIDA,
                QuoteItem::ITEM => $item->ITEM,
                QuoteItem::QUANTITY => $item->QUANTIDADE,
                QuoteItem::CURRENCY => $currency->iso_code,
                QuoteItem::UNIT_PRICE => $item->PRECO_UNITARIO,
                QuoteItem::COMMENTS => $item->OBS,
            ]);
        }

        $quote->markAsPending();
        $quote->refresh();

        return $quote;
    }

    /** @throws ModelNotFoundException */
    private function getCompany(ProtheusQuotePayloadData $data): Company
    {
        /** @var Company $company */
        $company = Company::query()
            ->where(Company::CODE, '=', $data->EMPRESA)
            ->where(Company::CODE_BRANCH, '=', $data->FILIAL)
            ->firstOrFail();

        return $company;
    }
}

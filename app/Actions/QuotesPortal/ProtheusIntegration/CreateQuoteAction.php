<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\Quote\ProtheusQuotePayloadData;
use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Models\QuotesPortal\Budget;
use App\Models\QuotesPortal\Currency;
use App\Models\QuotesPortal\PaymentCondition;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\Supplier;
use App\Models\User;

class CreateQuoteAction
{
    public function handle(
        Budget $budget,
        Currency $currency,
        Supplier $supplier,
        PaymentCondition $paymentCondition,
        User $buyer,
        ProtheusQuotePayloadData $data
    ): Quote {
        return $budget->quotes()->create([
            Quote::COMPANY_CODE => $data->EMPRESA,
            Quote::COMPANY_CODE_BRANCH => $data->FILIAL,
            Quote::SUPPLIER_ID => $supplier->id,
            Quote::PAYMENT_CONDITION_ID => $paymentCondition->id,
            Quote::BUYER_ID => $buyer->id,
            Quote::QUOTE_NUMBER => $data->COTACAO,
            Quote::STATUS => QuoteStatusEnum::DRAFT,
            Quote::COMMENTS => $data->OBSERVACAO_GERAL,
            Quote::CURRENCY_ID => $currency->id,
        ]);
    }
}

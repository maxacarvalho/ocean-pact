<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\Quote\ProtheusQuotePayloadData;
use App\Models\QuotesPortal\PaymentCondition;

class FindOrCreatePaymentConditionAction
{
    public function handle(ProtheusQuotePayloadData $data): PaymentCondition
    {
        /** @var PaymentCondition $paymentCondition */
        $paymentCondition = PaymentCondition::query()
            ->firstOrCreate([
                PaymentCondition::CODE => $data->COND_PAGTO->CODIGO,
                PaymentCondition::COMPANY_CODE => $data->EMPRESA,
                PaymentCondition::COMPANY_CODE_BRANCH => $data->COND_PAGTO->FILIAL,
                PaymentCondition::DESCRIPTION => $data->COND_PAGTO->DESCRICAO,
            ]);

        return $paymentCondition;
    }
}

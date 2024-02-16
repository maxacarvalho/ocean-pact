<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\StoreQuotePayloadData;
use App\Models\QuotesPortal\PaymentCondition;

class FindOrCreatePaymentConditionAction
{
    public function handle(StoreQuotePayloadData $data): PaymentCondition
    {
        /** @var PaymentCondition $paymentCondition */
        $paymentCondition = PaymentCondition::query()->firstOrCreate(
            [
                PaymentCondition::COMPANY_CODE => $data->companyCode,
                PaymentCondition::COMPANY_CODE_BRANCH => $data->companyCodeBranch,
                PaymentCondition::CODE => $data->paymentCondition->code,
            ],
            [
                PaymentCondition::DESCRIPTION => $data->paymentCondition->description,
            ]
        );

        return $paymentCondition;
    }
}

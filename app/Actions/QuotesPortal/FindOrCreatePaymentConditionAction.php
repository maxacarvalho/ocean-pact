<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\QuoteData;
use App\Models\QuotesPortal\PaymentCondition;

class FindOrCreatePaymentConditionAction
{
    public function handle(QuoteData $data): PaymentCondition
    {
        /** @var PaymentCondition $paymentCondition */
        $paymentCondition = PaymentCondition::query()
            ->firstOrCreate([
                PaymentCondition::CODE => $data->paymentCondition->code,
                PaymentCondition::COMPANY_CODE => $data->company_code,
                PaymentCondition::COMPANY_CODE_BRANCH => $data->company_code_branch,
                PaymentCondition::DESCRIPTION => $data->paymentCondition->description,
            ]);

        return $paymentCondition;
    }
}

<?php

namespace App\Actions\IntegraHub;

use App\Data\QuotesPortal\PaymentConditionData;
use App\Models\QuotesPortal\PaymentCondition;

class UpdateOrCreatePaymentConditionAction
{
    public function handle(PaymentConditionData $paymentConditionData): PaymentCondition
    {
        /** @var PaymentCondition $paymentCondition */
        $paymentCondition = PaymentCondition::query()->updateOrCreate([
            PaymentCondition::COMPANY_CODE => $paymentConditionData->company_code,
            PaymentCondition::COMPANY_CODE_BRANCH => $paymentConditionData->company_code_branch,
            PaymentCondition::CODE => $paymentConditionData->code,
        ], [
            PaymentCondition::DESCRIPTION => $paymentConditionData->description,
        ]);

        return $paymentCondition;
    }
}

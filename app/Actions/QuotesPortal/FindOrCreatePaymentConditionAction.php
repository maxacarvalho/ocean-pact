<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\QuoteData;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\PaymentCondition;

class FindOrCreatePaymentConditionAction
{
    public function handle(QuoteData $data, Company $company): PaymentCondition
    {
        /** @var PaymentCondition $paymentCondition */
        $paymentCondition = PaymentCondition::query()
            ->firstOrCreate([
                PaymentCondition::CODE => $data->paymentCondition->code,
                PaymentCondition::COMPANY_CODE => $company->code,
                PaymentCondition::COMPANY_CODE_BRANCH => $company->code_branch,
                PaymentCondition::DESCRIPTION => $data->paymentCondition->description,
            ]);

        return $paymentCondition;
    }
}

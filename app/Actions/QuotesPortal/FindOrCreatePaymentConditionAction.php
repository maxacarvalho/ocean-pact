<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\QuoteData;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\PaymentCondition;

class FindOrCreatePaymentConditionAction
{
    public function handle(QuoteData $data, Company $company): PaymentCondition
    {
        /** @var PaymentCondition|null $paymentCondition */
        $paymentCondition = PaymentCondition::query()
            ->where(PaymentCondition::CODE, '=', $data->paymentCondition->code)
            ->where(PaymentCondition::COMPANY_CODE, '=', $company->code)
            ->where(PaymentCondition::COMPANY_CODE_BRANCH, '=', $company->code_branch)
            ->first();

        if (null === $paymentCondition) {
            $paymentCondition = PaymentCondition::query()->create([
                PaymentCondition::CODE => $data->paymentCondition->code,
                PaymentCondition::COMPANY_CODE => $company->code,
                PaymentCondition::COMPANY_CODE_BRANCH => $company->code_branch,
                PaymentCondition::DESCRIPTION => $data->paymentCondition->description,
            ]);
        }

        $paymentCondition->description = $data->paymentCondition->description;
        $paymentCondition->save();

        return $paymentCondition;
    }
}

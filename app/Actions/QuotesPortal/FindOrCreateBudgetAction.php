<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\QuoteData;
use App\Models\QuotesPortal\Budget;
use App\Models\QuotesPortal\Company;

class FindOrCreateBudgetAction
{
    public function handle(QuoteData $data, Company $company): Budget
    {
        /** @var Budget|null $budget */
        $budget = Budget::query()
            ->where(Budget::COMPANY_CODE, '=', $company->code)
            ->where(Budget::COMPANY_CODE_BRANCH, '=', $company->code_branch)
            ->where(Budget::BUDGET_NUMBER, '=', $data->budget->budget_number)
            ->first();

        if (null === $budget) {
            /** @var Budget $budget */
            $budget = Budget::query()->create([
                Budget::COMPANY_CODE => $company->code,
                Budget::COMPANY_CODE_BRANCH => $company->code_branch,
                Budget::BUDGET_NUMBER => $data->budget->budget_number,
            ]);
        }

        return $budget;
    }
}

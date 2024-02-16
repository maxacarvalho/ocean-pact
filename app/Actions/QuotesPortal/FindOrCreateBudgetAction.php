<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\StoreQuotePayloadData;
use App\Models\QuotesPortal\Budget;

class FindOrCreateBudgetAction
{
    public function handle(StoreQuotePayloadData $data): Budget
    {
        /** @var Budget $budget */
        $budget = Budget::query()->firstOrCreate([
            Budget::COMPANY_CODE => $data->companyCode,
            Budget::COMPANY_CODE_BRANCH => $data->companyCodeBranch,
            Budget::BUDGET_NUMBER => $data->budget->budgetNumber,
        ]);

        return $budget;
    }
}

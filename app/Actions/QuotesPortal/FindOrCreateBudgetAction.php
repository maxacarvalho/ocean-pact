<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\QuoteData;
use App\Models\QuotesPortal\Budget;

class FindOrCreateBudgetAction
{
    public function handle(QuoteData $data): Budget
    {
        /** @var Budget $budget */
        $budget = Budget::query()->firstOrCreate([
            Budget::COMPANY_CODE => $data->company_code,
            Budget::COMPANY_CODE_BRANCH => $data->company_code_branch,
            Budget::BUDGET_NUMBER => $data->budget->budget_number,
        ]);

        return $budget;
    }
}

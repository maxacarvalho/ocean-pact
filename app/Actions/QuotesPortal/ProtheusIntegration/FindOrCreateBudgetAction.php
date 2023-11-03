<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\Quote\ProtheusQuotePayloadData;
use App\Models\QuotesPortal\Budget;

class FindOrCreateBudgetAction
{
    public function handle(ProtheusQuotePayloadData $data): Budget
    {
        /** @var Budget $budget */
        $budget = Budget::query()->firstOrCreate([
            Budget::COMPANY_CODE => $data->EMPRESA,
            Budget::COMPANY_CODE_BRANCH => $data->FILIAL,
            Budget::BUDGET_NUMBER => $data->SOLICITACAO_DE_COMPRAS,
        ]);

        return $budget;
    }
}

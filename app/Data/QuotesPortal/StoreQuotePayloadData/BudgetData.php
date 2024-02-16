<?php

namespace App\Data\QuotesPortal\StoreQuotePayloadData;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class BudgetData extends Data
{
    public function __construct(
        #[MapInputName('budget_number')]
        public readonly string $budgetNumber
    ) {
        //
    }
}

<?php

namespace App\Data\QuotesPortal;

use App\Data\QuotesPortal\StoreQuotePayloadData\BudgetData;
use App\Data\QuotesPortal\StoreQuotePayloadData\BuyerData;
use App\Data\QuotesPortal\StoreQuotePayloadData\CurrencyData;
use App\Data\QuotesPortal\StoreQuotePayloadData\ItemData;
use App\Data\QuotesPortal\StoreQuotePayloadData\PaymentConditionData;
use App\Data\QuotesPortal\StoreQuotePayloadData\SupplierData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class StoreQuotePayloadData extends Data
{
    /** @var Collection<int, ItemData> */
    public Collection $items;
    /** @var Collection<int, SupplierData> */
    public Collection $suppliers;

    public function __construct(
        #[MapInputName('company_code')]
        public readonly string $companyCode,
        #[MapInputName('company_code_branch')]
        public readonly string $companyCodeBranch,
        #[MapInputName('proposal_number')]
        public readonly string $proposalNumber,
        #[MapInputName('quote_number')]
        public readonly string $quoteNumber,
        public readonly string $comments,
        public readonly BudgetData $budget,
        public readonly CurrencyData $currency,
        public readonly PaymentConditionData $paymentCondition,
        public readonly BuyerData $buyer,
    ) {
        //
    }
}

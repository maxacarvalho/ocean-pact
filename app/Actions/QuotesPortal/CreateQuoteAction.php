<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\QuoteData;
use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Models\QuotesPortal\Budget;
use App\Models\QuotesPortal\Currency;
use App\Models\QuotesPortal\PaymentCondition;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\Supplier;
use App\Models\User;

class CreateQuoteAction
{
    public function handle(
        Budget $budget,
        Currency $currency,
        Supplier $supplier,
        PaymentCondition $paymentCondition,
        User $buyer,
        QuoteData $data
    ): Quote {
        return $budget->quotes()->create([
            Quote::VERSION => $data->version,
            Quote::COMPANY_ID => $data->company_id,
            Quote::SUPPLIER_ID => $supplier->id,
            Quote::PAYMENT_CONDITION_ID => $paymentCondition->id,
            Quote::BUYER_ID => $buyer->id,
            Quote::QUOTE_NUMBER => $data->quote_number,
            Quote::STATUS => QuoteStatusEnum::DRAFT,
            Quote::COMMENTS => $data->comments,
            Quote::CURRENCY_ID => $currency->id,
        ]);
    }
}

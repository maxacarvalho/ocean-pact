<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\StoreQuotePayloadData;
use App\Models\QuotesPortal\PaymentCondition;

class FindOrCreatePaymentConditionAction
{
    public function handle(StoreQuotePayloadData $data): PaymentCondition
    {
        /** @var PaymentCondition */
        return PaymentCondition::query()->firstOrCreate(
            [
                PaymentCondition::CODE => $data->paymentCondition->code,
            ],
            [
                PaymentCondition::DESCRIPTION => $data->paymentCondition->description,
            ]
        );
    }
}

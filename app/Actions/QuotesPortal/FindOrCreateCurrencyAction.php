<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\StoreQuotePayloadData;
use App\Models\QuotesPortal\Currency;

class FindOrCreateCurrencyAction
{
    public function handle(StoreQuotePayloadData $data): Currency
    {
        /** @var Currency $currency */
        $currency = Currency::query()->firstOrCreate(
            [
                Currency::COMPANY_CODE => $data->companyCode,
                Currency::PROTHEUS_CURRENCY_ID => $data->currency->protheusCurrencyId,
                Currency::PROTHEUS_CODE => $data->currency->protheusCode,
                Currency::PROTHEUS_ACRONYM => $data->currency->protheusAcronym,
                Currency::ISO_CODE => $data->currency->protheusAcronym,
            ],
            [
                Currency::DESCRIPTION => $data->currency->description,
            ]
        );

        return $currency;
    }
}

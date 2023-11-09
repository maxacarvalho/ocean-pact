<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\QuoteData;
use App\Models\QuotesPortal\Currency;

class FindOrCreateCurrencyAction
{
    public function handle(QuoteData $data): Currency
    {
        /** @var Currency $currency */
        $currency = Currency::query()
            ->firstOrCreate([
                Currency::COMPANY_CODE => $data->company_code,
                Currency::PROTHEUS_CURRENCY_ID => $data->currency->protheus_currency_id,
                Currency::DESCRIPTION => $data->currency->description,
                Currency::PROTHEUS_CODE => $data->currency->protheus_code,
                Currency::PROTHEUS_ACRONYM => $data->currency->protheus_acronym,
                Currency::ISO_CODE => $data->currency->protheus_acronym,
            ]);

        return $currency;
    }
}

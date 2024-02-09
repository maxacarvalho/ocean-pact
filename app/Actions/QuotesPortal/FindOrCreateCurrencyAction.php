<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\QuoteData;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Currency;

class FindOrCreateCurrencyAction
{
    public function handle(QuoteData $data, Company $company): Currency
    {
        /** @var Currency|null $currency */
        $currency = Currency::query()
            ->where(Currency::COMPANY_CODE, '=', $company->code)
            ->where(Currency::PROTHEUS_CURRENCY_ID, '=', $data->currency->protheus_currency_id)
            ->where(Currency::DESCRIPTION, '=', $data->currency->description)
            ->where(Currency::PROTHEUS_CODE, '=', $data->currency->protheus_code)
            ->where(Currency::PROTHEUS_ACRONYM, '=', $data->currency->protheus_acronym)
            ->where(Currency::ISO_CODE, '=', $data->currency->protheus_acronym)
            ->first();

        if (null === $currency) {
            /** @var Currency $currency */
            $currency = Currency::query()->create([
                Currency::COMPANY_CODE => $company->code,
                Currency::PROTHEUS_CURRENCY_ID => $data->currency->protheus_currency_id,
                Currency::DESCRIPTION => $data->currency->description,
                Currency::PROTHEUS_CODE => $data->currency->protheus_code,
                Currency::PROTHEUS_ACRONYM => $data->currency->protheus_acronym,
                Currency::ISO_CODE => $data->currency->protheus_acronym,
            ]);
        }

        return $currency;
    }
}

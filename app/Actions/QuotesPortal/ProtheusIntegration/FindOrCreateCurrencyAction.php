<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\Quote\ProtheusQuotePayloadData;
use App\Models\QuotesPortal\Currency;

class FindOrCreateCurrencyAction
{
    public function handle(ProtheusQuotePayloadData $data): Currency
    {
        /** @var Currency $currency */
        $currency = Currency::query()
            ->firstOrCreate([
                Currency::COMPANY_CODE => $data->MOEDAS->EMPRESA,
                Currency::PROTHEUS_CURRENCY_ID => $data->MOEDAS->MOEDA,
                Currency::DESCRIPTION => $data->MOEDAS->DESCRICAO,
                Currency::PROTHEUS_CODE => $data->MOEDAS->CODIGO,
                Currency::PROTHEUS_ACRONYM => $data->MOEDAS->SIGLA,
                Currency::ISO_CODE => $data->MOEDAS->SIGLA,
            ]);

        return $currency;
    }
}

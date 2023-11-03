<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\Quote\ProtheusQuotePayloadData;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Supplier;

class FindOrCreateSupplierAction
{
    public function handle(ProtheusQuotePayloadData $data, Company $company): Supplier
    {
        /** @var Supplier|null $supplier */
        $supplier = Supplier::query()
            ->with(Supplier::RELATION_USERS, Supplier::RELATION_COMPANIES)
            ->where(Supplier::CODE, '=', $data->FORNECEDOR->CODIGO)
            ->where(Supplier::STORE, '=', $data->FORNECEDOR->LOJA)
            ->first();

        if (null === $supplier) {
            $supplier = Supplier::query()->create([
                Supplier::COMPANY_CODE => $data->EMPRESA,
                Supplier::COMPANY_CODE_BRANCH => $data->FILIAL,
                Supplier::CODE => $data->FORNECEDOR->CODIGO,
                Supplier::STORE => $data->FORNECEDOR->LOJA,
                Supplier::NAME => $data->FORNECEDOR->NOME_FORNECEDOR,
                Supplier::BUSINESS_NAME => $data->FORNECEDOR->NOME_FANTASIA,
                Supplier::STATE_CODE => $data->FORNECEDOR->UF,
                Supplier::EMAIL => $data->FORNECEDOR->EMAIL,
                Supplier::CONTACT => $data->FORNECEDOR->CONTATO,
            ]);
        }

        if (! $supplier->companies->contains($company->id)) {
            $supplier->companies()->attach($company->id);
        }

        return $supplier;
    }
}

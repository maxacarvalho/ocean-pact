<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\QuoteData;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Supplier;

class FindOrCreateSupplierAction
{
    public function handle(QuoteData $data, Company $company): Supplier
    {
        /** @var Supplier|null $supplier */
        $supplier = Supplier::query()
            ->with(Supplier::RELATION_USERS, Supplier::RELATION_COMPANIES)
            ->where(Supplier::CODE, '=', $data->supplier->code)
            ->where(Supplier::STORE, '=', $data->supplier->store)
            ->first();

        if (null === $supplier) {
            $supplier = Supplier::query()->create([
                Supplier::COMPANY_CODE => $data->company_code,
                Supplier::COMPANY_CODE_BRANCH => $data->company_code_branch,
                Supplier::CODE => $data->supplier->code,
                Supplier::STORE => $data->supplier->store,
                Supplier::NAME => $data->supplier->name,
                Supplier::BUSINESS_NAME => $data->supplier->business_name,
                Supplier::ADDRESS => $data->supplier->address,
                Supplier::NUMBER => $data->supplier->number,
                Supplier::STATE_CODE => $data->supplier->state_code,
                Supplier::POSTAL_CODE => $data->supplier->postal_code,
                Supplier::CNPJ_CPF => $data->supplier->cnpj_cpf,
                Supplier::PHONE_CODE => $data->supplier->phone_code,
                Supplier::PHONE_NUMBER => $data->supplier->phone_number,
                Supplier::CONTACT => $data->supplier->contact,
                Supplier::EMAIL => $data->supplier->email,
            ]);
        }

        if (! $supplier->companies->contains($company->id)) {
            $supplier->companies()->attach($company->id);
        }

        return $supplier;
    }
}

<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\StoreQuotePayloadData;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Supplier;
use App\Models\QuotesPortal\SupplierUser;
use Spatie\LaravelData\Optional;

readonly class FindOrCreateSuppliersAction
{
    public function __construct(
        private CreateSellerAction $createSellerAction
    ) {
        //
    }

    /** @return array<string, int> */
    public function handle(StoreQuotePayloadData $data, Company $company): array
    {
        $mappingCodesAndStoresToSuppliersIds = [];

        foreach ($data->suppliers as $supplierData) {
            /** @var Supplier $supplier */
            $supplier = Supplier::query()->firstOrCreate(
                [
                    Supplier::COMPANY_CODE => $company->code,
                    Supplier::COMPANY_CODE_BRANCH => $company->code_branch,
                    Supplier::CODE => $supplierData->code,
                    Supplier::STORE => $supplierData->store,
                ],
                [
                    Supplier::NAME => $supplierData->name,
                    Supplier::BUSINESS_NAME => $supplierData->businessName instanceof Optional ? null : $supplierData->businessName,
                    Supplier::ADDRESS => $supplierData->address instanceof Optional ? null : $supplierData->address,
                    Supplier::NUMBER => $supplierData->number instanceof Optional ? null : $supplierData->number,
                    Supplier::STATE_CODE => $supplierData->stateCode instanceof Optional ? null : $supplierData->stateCode,
                    Supplier::POSTAL_CODE => $supplierData->postalCode instanceof Optional ? null : $supplierData->postalCode,
                    Supplier::CNPJ_CPF => $supplierData->cnpjCpf instanceof Optional ? null : $supplierData->cnpjCpf,
                    Supplier::PHONE_CODE => $supplierData->phoneCode instanceof Optional ? null : $supplierData->phoneCode,
                    Supplier::PHONE_NUMBER => $supplierData->phoneNumber instanceof Optional ? null : $supplierData->phoneNumber,
                ]
            );

            if (! $supplier->companies->contains($company->id)) {
                $supplier->companies()->attach($company->id);
            }

            $sellers = [];
            foreach ($supplierData->sellers as $sellerData) {
                $seller = $this->createSellerAction->handle($sellerData);
                $sellers[$seller->id] = [SupplierUser::CODE => $sellerData->supplierUser->code];
            }

            $supplier->sellers()->sync($sellers);

            $mappingCodesAndStoresToSuppliersIds[$supplier->code.'-'.$supplier->store] = $supplier->id;
        }

        return $mappingCodesAndStoresToSuppliersIds;
    }
}

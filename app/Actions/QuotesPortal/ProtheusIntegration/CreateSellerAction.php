<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\Quote\ProtheusSellerPayloadData;
use App\Models\QuotesPortal\Supplier;
use App\Models\QuotesPortal\SupplierUser;
use App\Models\Role;
use App\Models\User;
use App\Utils\Str;

class CreateSellerAction
{
    public function handle(ProtheusSellerPayloadData $data, Supplier $supplier)
    {
        /** @var User|null $seller */
        $seller = User::query()
            ->where(User::EMAIL, '=', $data->EMAIL)
            ->first();

        if (null === $seller) {
            $seller = User::query()
                ->create([
                    User::NAME => $data->NOME,
                    User::EMAIL => $data->EMAIL,
                    User::IS_DRAFT => true,
                    User::ACTIVE => $data->STATUS,
                    User::PASSWORD => bcrypt(Str::random(30)),
                ]);
        }

        /** @var SupplierUser|null $supplierUser */
        $supplierUser = SupplierUser::query()
            ->where(SupplierUser::USER_ID, '=', $seller->id)
            ->where(SupplierUser::SUPPLIER_ID, '=', $supplier->id)
            ->where(SupplierUser::CODE, '=', $data->CODIGO)
            ->first();

        if (null === $supplierUser) {
            SupplierUser::query()
                ->create([
                    SupplierUser::USER_ID => $seller->id,
                    SupplierUser::SUPPLIER_ID => $supplier->id,
                    SupplierUser::CODE => $data->CODIGO,
                ]);
        }

        if (false === $seller->hasRole(Role::ROLE_SELLER)) {
            $seller->assignRole(Role::ROLE_SELLER);
        }
    }
}

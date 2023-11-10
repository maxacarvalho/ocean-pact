<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\SellerData;
use App\Models\QuotesPortal\Supplier;
use App\Models\QuotesPortal\SupplierUser;
use App\Models\Role;
use App\Models\User;
use App\Utils\Str;

class CreateSellerAction
{
    public function handle(SellerData $data, Supplier $supplier): void
    {
        /** @var User|null $seller */
        $seller = User::query()
            ->where(User::EMAIL, '=', $data->email)
            ->first();

        if (null === $seller) {
            $seller = User::query()
                ->create([
                    User::NAME => $data->name,
                    User::EMAIL => $data->email,
                    User::IS_DRAFT => true,
                    User::ACTIVE => $data->active,
                    User::PASSWORD => bcrypt(Str::random(30)),
                ]);
        }

        /** @var SupplierUser|null $supplierUser */
        $supplierUser = SupplierUser::query()
            ->where(SupplierUser::USER_ID, '=', $seller->id)
            ->where(SupplierUser::SUPPLIER_ID, '=', $supplier->id)
            ->where(SupplierUser::CODE, '=', $data->seller_code)
            ->first();

        if (null === $supplierUser) {
            SupplierUser::query()
                ->create([
                    SupplierUser::USER_ID => $seller->id,
                    SupplierUser::SUPPLIER_ID => $supplier->id,
                    SupplierUser::CODE => $data->seller_code,
                ]);
        }

        if (false === $seller->hasRole(Role::ROLE_SELLER)) {
            $seller->assignRole(Role::ROLE_SELLER);
        }
    }
}

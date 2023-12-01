<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\SellerData;
use App\Models\Role;
use App\Models\User;
use App\Utils\Str;

class CreateSellerAction
{
    public function handle(SellerData $data): User
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

        if (false === $seller->hasRole(Role::ROLE_SELLER)) {
            $seller->assignRole(Role::ROLE_SELLER);
        }

        return $seller;
    }
}

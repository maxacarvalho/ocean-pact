<?php

namespace App\Actions\QuotesPortal;

use App\Data\QuotesPortal\StoreQuotePayloadData\SellerData;
use App\Models\Role;
use App\Models\User;
use App\Utils\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateSellerAction
{
    /** @throws ValidationException */
    public function handle(SellerData $data): User
    {
        /** @var User|null $seller */
        $seller = User::query()
            ->where(User::EMAIL, '=', $data->email)
            ->first();

        if (null === $seller) {
            Validator::make([
                User::EMAIL => $data->email,
            ], [
                User::EMAIL => ['required', 'email'],
            ])->validate();

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

<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\QuoteData;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\CompanyUser;
use App\Models\Role;
use App\Models\User;
use App\Utils\Str;

class FindOrCreateBuyerAction
{
    public function handle(QuoteData $data, Company $company): User
    {
        /** @var User|null $buyer */
        $buyer = User::query()
            ->with([User::RELATION_COMPANIES])
            ->where(User::EMAIL, '=', $data->buyer->email)
            ->first();

        if (null === $buyer) {
            /** @var User $buyer */
            $buyer = User::query()->create([
                User::NAME => $data->buyer->name,
                User::EMAIL => $data->buyer->email,
                User::PASSWORD => bcrypt(Str::random(30)),
                User::IS_DRAFT => true,
            ]);
        }

        /** @var CompanyUser|null $companyUser */
        $companyUser = CompanyUser::query()
            ->where(CompanyUser::USER_ID, '=', $buyer->id)
            ->where(CompanyUser::COMPANY_ID, '=', $company->id)
            ->where(CompanyUser::BUYER_CODE, '=', $data->buyer->buyer_code)
            ->first();

        if (null === $companyUser) {
            CompanyUser::query()
                ->create([
                    CompanyUser::USER_ID => $buyer->id,
                    CompanyUser::COMPANY_ID => $company->id,
                    CompanyUser::BUYER_CODE => $data->buyer->buyer_code,
                ]);
        }

        if (false === $buyer->hasRole(Role::ROLE_BUYER)) {
            $buyer->assignRole(Role::ROLE_BUYER);
        }

        return $buyer;
    }
}

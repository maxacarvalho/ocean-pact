<?php

namespace App\Actions\QuotesPortal\ProtheusIntegration;

use App\Data\QuotesPortal\Quote\ProtheusQuotePayloadData;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\CompanyUser;
use App\Models\Role;
use App\Models\User;
use App\Utils\Str;

class FindOrCreateBuyerAction
{
    public function handle(ProtheusQuotePayloadData $data, Company $company): User
    {
        /** @var User|null $buyer */
        $buyer = User::query()
            ->with([User::RELATION_COMPANIES])
            ->where(User::EMAIL, '=', $data->COMPRADOR->EMAIL)
            ->first();

        if (null === $buyer) {
            /** @var User $buyer */
            $buyer = User::query()->create([
                User::NAME => $data->COMPRADOR->NOME,
                User::EMAIL => $data->COMPRADOR->EMAIL,
                User::PASSWORD => bcrypt(Str::random(30)),
                User::IS_DRAFT => true,
            ]);
        }

        /** @var CompanyUser|null $companyUser */
        $companyUser = CompanyUser::query()
            ->where(CompanyUser::USER_ID, '=', $buyer->id)
            ->where(CompanyUser::COMPANY_ID, '=', $company->id)
            ->where(CompanyUser::BUYER_CODE, '=', $data->COMPRADOR->CODIGO)
            ->first();

        if (null === $companyUser) {
            CompanyUser::query()
                ->create([
                    CompanyUser::USER_ID => $buyer->id,
                    CompanyUser::COMPANY_ID => $company->id,
                    CompanyUser::BUYER_CODE => $data->COMPRADOR->CODIGO,
                ]);
        }

        if (false === $buyer->hasRole(Role::ROLE_BUYER)) {
            $buyer->assignRole(Role::ROLE_BUYER);
        }

        return $buyer;
    }
}

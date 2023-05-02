<?php

namespace App\Filament\Resources\PaymentConditionResource\Pages;

use App\Filament\Resources\PaymentConditionResource;
use App\Models\Company;
use App\Models\PaymentCondition;
use Filament\Pages\Actions\CreateAction as PageCreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Query\Builder;

class ListPaymentConditions extends ListRecords
{
    protected static string $resource = PaymentConditionResource::class;

    protected function getActions(): array
    {
        return [
            PageCreateAction::make(),
        ];
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getTableQuery()
            ->select([
                PaymentCondition::TABLE_NAME.'.'.PaymentCondition::ID,
                PaymentCondition::TABLE_NAME.'.'.PaymentCondition::COMPANY_CODE,
                PaymentCondition::TABLE_NAME.'.'.PaymentCondition::COMPANY_CODE_BRANCH,
                PaymentCondition::TABLE_NAME.'.'.PaymentCondition::CODE,
                PaymentCondition::TABLE_NAME.'.'.PaymentCondition::DESCRIPTION,
                PaymentCondition::TABLE_NAME.'.'.PaymentCondition::CREATED_AT,
                PaymentCondition::TABLE_NAME.'.'.PaymentCondition::UPDATED_AT,
            ])
            ->addSelect([
                'company_name' => fn (Builder $query) => $query->select(Company::BUSINESS_NAME)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE,
                        '=',
                        PaymentCondition::TABLE_NAME.'.'.PaymentCondition::COMPANY_CODE
                    )
                    ->limit(1),
                'company_branch' => fn (Builder $query) => $query->select(Company::BRANCH)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE,
                        '=',
                        PaymentCondition::TABLE_NAME.'.'.PaymentCondition::COMPANY_CODE
                    )
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                        '=',
                        PaymentCondition::TABLE_NAME.'.'.PaymentCondition::COMPANY_CODE_BRANCH
                    )
                    ->limit(1),
            ]);
    }
}

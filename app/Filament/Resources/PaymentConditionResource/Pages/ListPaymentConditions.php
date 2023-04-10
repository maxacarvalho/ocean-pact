<?php

namespace App\Filament\Resources\PaymentConditionResource\Pages;

use App\Filament\Resources\PaymentConditionResource;
use App\Models\Company;
use App\Models\PaymentCondition;
use Filament\Pages\Actions\CreateAction as PageCreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPaymentConditions extends ListRecords
{
    protected static string $resource = PaymentConditionResource::class;

    protected function getActions(): array
    {
        return [
            PageCreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->select([
                PaymentCondition::TABLE_NAME.'.*',
                Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                Company::TABLE_NAME.'.'.Company::BRANCH,
            ])
            ->join(
                Company::TABLE_NAME,
                Company::TABLE_NAME.'.'.Company::ID,
                '=',
                PaymentCondition::TABLE_NAME.'.'.PaymentCondition::COMPANY_ID
            );
    }
}

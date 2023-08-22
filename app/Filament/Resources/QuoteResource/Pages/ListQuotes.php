<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Enums\QuoteStatusEnum;
use App\Filament\Resources\QuoteResource;
use App\Models\Company;
use App\Models\Quote;
use App\Models\User;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use Illuminate\Support\Facades\Auth;

class ListQuotes extends ListRecords
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // PageCreateAction::make(),
        ];
    }

    protected function shouldPersistTableFiltersInSession(): bool
    {
        return true;
    }

    protected function shouldPersistTableSortInSession(): bool
    {
        return true;
    }

    protected function getTableQuery(): Builder
    {
        /** @var User $user */
        $user = Auth::user();

        return parent::getTableQuery()
            ->select([
                Quote::TABLE_NAME.'.'.Quote::ID,
                Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE,
                Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE_BRANCH,
                Quote::TABLE_NAME.'.'.Quote::BUDGET_ID,
                Quote::TABLE_NAME.'.'.Quote::SUPPLIER_ID,
                Quote::TABLE_NAME.'.'.Quote::PAYMENT_CONDITION_ID,
                Quote::TABLE_NAME.'.'.Quote::BUYER_ID,
                Quote::TABLE_NAME.'.'.Quote::QUOTE_NUMBER,
                Quote::TABLE_NAME.'.'.Quote::VALID_UNTIL,
                Quote::TABLE_NAME.'.'.Quote::STATUS,
                Quote::TABLE_NAME.'.'.Quote::COMMENTS,
                Quote::TABLE_NAME.'.'.Quote::CREATED_AT,
                Quote::TABLE_NAME.'.'.Quote::UPDATED_AT,
            ])
            ->where(Quote::TABLE_NAME.'.'.Quote::STATUS, '!=', QuoteStatusEnum::DRAFT)
            ->addSelect([
                'company_name' => fn (DbQueryBuilder $query) => $query->select(Company::BUSINESS_NAME)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE,
                        '=',
                        Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE
                    )
                    ->limit(1),
                'company_branch' => fn (DbQueryBuilder $query) => $query->select(Company::BRANCH)
                    ->from(Company::TABLE_NAME)
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE,
                        '=',
                        Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE
                    )
                    ->whereColumn(
                        Company::TABLE_NAME.'.'.Company::CODE_BRANCH,
                        '=',
                        Quote::TABLE_NAME.'.'.Quote::COMPANY_CODE_BRANCH
                    )
                    ->limit(1),
            ])
            ->when($user->isSeller(), function (Builder $query) use ($user) {
                $query->where(Quote::TABLE_NAME.'.'.Quote::SUPPLIER_ID, '=', $user->supplier_id);
            });
    }
}

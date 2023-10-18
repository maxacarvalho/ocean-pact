<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Enums\QuoteStatusEnum;
use App\Filament\Resources\QuoteResource;
use App\Filament\Resources\QuoteResource\Widgets\QuotesOverviewWidget;
use App\Models\Company;
use App\Models\Quote;
use App\Models\Role;
use App\Models\User;
use App\Utils\Str;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as DbQueryBuilder;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListQuotes extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = QuoteResource::class;

    public function getTabs(): array
    {
        if (Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN)) {
            return [
                'all' => Tab::make(Str::ucfirst(__('quote.all'))),
                'pending' => Tab::make(Str::ucfirst(__('quote.pending')))
                    ->modifyQueryUsing(fn (Builder $query) => $query->where(Quote::STATUS, '=', QuoteStatusEnum::PENDING)),
                'responded' => Tab::make(Str::ucfirst(__('quote.responded')))
                    ->modifyQueryUsing(fn (Builder $query) => $query->where(Quote::STATUS, '=', QuoteStatusEnum::RESPONDED)),
                'analyzed' => Tab::make(Str::ucfirst(__('quote.analyzed')))
                    ->modifyQueryUsing(fn (Builder $query) => $query->where(Quote::STATUS, '=', QuoteStatusEnum::ANALYZED)),
            ];
        }

        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            // PageCreateAction::make(),
            ExportAction::make()
                ->label(Str::ucfirst(__('actions.export')))
                ->icon('far-download')
                ->exports([
                    ExcelExport::make()->fromTable()
                    ->withColumns([
                        Column::make(Quote::STATUS)
                            ->heading(Str::formatTitle(__('quote.status')))
                            ->formatStateUsing(function (QuoteStatusEnum $state) {
                                return $state->getLabel();
                            })
                    ])
                    ->queue()
                ])
        ];
    }

    protected function getHeaderWidgets(): array
    {
        if (Auth::user()->hasAnyRole(Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN)) {
            return [
                QuotesOverviewWidget::class,
            ];
        }

        return [];
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

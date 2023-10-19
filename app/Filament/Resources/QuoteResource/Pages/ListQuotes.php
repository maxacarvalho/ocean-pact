<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Enums\QuoteStatusEnum;
use App\Filament\Resources\QuoteResource;
use App\Filament\Resources\QuoteResource\Widgets\QuotesOverviewWidget;
use App\Models\Quote;
use App\Models\Role;
use App\Utils\Str;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
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
            ExportAction::make()->exports([
                ExcelExport::make()->fromTable()
                    ->withFilename(fn ($resource) => Str::slug($resource::getPluralModelLabel()).'-'.now()->format('Y-m-d')),
            ]),
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
}

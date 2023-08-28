<?php

namespace App\Filament\Resources\QuoteResource\Widgets;

use App\Filament\Resources\QuoteResource\Pages\ListQuotes;
use App\Utils\Str;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use stdClass;

class QuotesOverviewWidget extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListQuotes::class;
    }

    protected function getStats(): array
    {
        /** @var stdClass $cloned */
        $cloned = $this->getPageTableQuery()
            ->toBase()
            ->cloneWithout(['columns', 'orders', 'limit', 'offset'])
            ->select([
                DB::raw('count(*) as total'),
                DB::raw("COUNT(CASE WHEN status = 'PENDING' THEN 1 END) AS pending"),
                DB::raw("COUNT(CASE WHEN status = 'RESPONDED' THEN 1 END) AS responded"),
                DB::raw("COUNT(CASE WHEN status = 'ANALYZED' THEN 1 END) AS analyzed"),
            ])
            ->first();

        return [
            Stat::make(Str::ucfirst(__('quote.all')), $cloned->total),
            Stat::make(Str::ucfirst(__('quote.pending')), $cloned->pending),
            Stat::make(Str::ucfirst(__('quote.responded')), $cloned->responded),
            Stat::make(Str::ucfirst(__('quote.analyzed')), $cloned->analyzed),
        ];
    }
}

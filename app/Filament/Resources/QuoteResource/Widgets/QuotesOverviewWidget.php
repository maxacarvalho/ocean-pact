<?php

namespace App\Filament\Resources\QuoteResource\Widgets;

use App\Models\Quote;
use App\Utils\Str;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class QuotesOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return Auth::user()->isAdmin() || Auth::user()->isSuperAdmin();
    }

    protected function getStats(): array
    {
        $totalQuotes = Quote::query()->count();
        $countPerStatus = Quote::query()
            ->selectRaw(Quote::STATUS.', count(*) as count')
            ->groupBy(Quote::STATUS)
            ->get()
            ->map(function (Model|Quote $item) {
                $label = Str::lower($item->status->value);

                return Stat::make(
                    Str::ucfirst(__("quote.{$label}")),
                    $item->count
                );
            })
            ->toArray();

        return array_merge(
            [
                Stat::make(Str::ucfirst(__('quote.all')), $totalQuotes),
            ],
            $countPerStatus
        );
    }
}

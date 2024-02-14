<?php

namespace App\Http\Controllers\QuotesPortal\API;

use App\Data\QuotesPortal\QuoteData;
use App\Http\Controllers\Controller;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ListQuotesController extends Controller
{
    public function __invoke(Request $request): DataCollection|CursorPaginatedDataCollection|PaginatedDataCollection
    {
        $quotes = QueryBuilder::for(Quote::class)
            ->with([
                Quote::RELATION_BUDGET,
                Quote::RELATION_COMPANY,
                Quote::RELATION_SUPPLIER,
                Quote::RELATION_PAYMENT_CONDITION,
                Quote::RELATION_BUYER => [
                    User::RELATION_COMPANIES,
                ],
                Quote::RELATION_CURRENCY,
                Quote::RELATION_ITEMS => [
                    QuoteItem::RELATION_PRODUCT,
                ],
            ])
            ->allowedFilters([
                AllowedFilter::exact(Quote::QUOTE_NUMBER),
                AllowedFilter::exact(Quote::STATUS),
            ])
            ->whereNotNull('currency_id')
            ->paginate(100)
            ->appends($request->query());

        return QuoteData::collect($quotes);
    }
}

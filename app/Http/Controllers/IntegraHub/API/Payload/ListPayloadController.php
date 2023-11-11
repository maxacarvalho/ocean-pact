<?php

namespace App\Http\Controllers\IntegraHub\API\Payload;

use App\Data\IntegraHub\PayloadData;
use App\Http\Controllers\Controller;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\Payload;
use Illuminate\Http\Request;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ListPayloadController extends Controller
{
    public function __invoke(Request $request): DataCollection|CursorPaginatedDataCollection|PaginatedDataCollection
    {
        $payloads = QueryBuilder::for(Payload::class)
            ->with([
                Payload::RELATION_INTEGRATION_TYPE,
            ])
            ->allowedFilters([
                AllowedFilter::exact(Payload::STORING_STATUS),
                AllowedFilter::exact(Payload::PROCESSING_STATUS),
                AllowedFilter::exact(Payload::RELATION_INTEGRATION_TYPE.'.'.IntegrationType::CODE),
            ])
            ->defaultSort('-'.Payload::STORED_AT)
            ->paginate($request->query('limit', 10))
            ->appends($request->query());

        return PayloadData::collection(
            $payloads
        );
    }
}

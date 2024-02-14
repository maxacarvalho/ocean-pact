<?php

namespace App\Http\Controllers\IntegraHub\API\Payload;

use App\Data\IntegraHub\PayloadData;
use App\Http\Controllers\Controller;
use App\Http\Requests\IntegraHub\ListPayloadRequest;
use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\Payload;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ListPayloadController extends Controller
{
    public function __invoke(ListPayloadRequest $request): DataCollection|CursorPaginatedDataCollection|PaginatedDataCollection
    {
        $perPage = $request->integer('perPage', 15);

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
            ->paginate($perPage)
            ->appends($request->query());

        return PayloadData::collect(
            $payloads
        );
    }
}

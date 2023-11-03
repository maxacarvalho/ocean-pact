<?php

namespace App\Http\Controllers\IntegraHub\API\Payload;

use App\Data\IntegraHub\PayloadData;
use App\Enums\IntegraHub\PayloadProcessingStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\IntegraHub\Payload;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

class IndexPayloadController extends Controller
{
    public function __invoke(): DataCollection|CursorPaginatedDataCollection|PaginatedDataCollection
    {
        return PayloadData::collection(
            Payload::query()
                ->where(Payload::PROCESSING_STATUS, '!=', PayloadProcessingStatusEnum::COLLECTED)
                ->orWhereNull(Payload::PROCESSING_STATUS)
                ->get()
        );
    }
}

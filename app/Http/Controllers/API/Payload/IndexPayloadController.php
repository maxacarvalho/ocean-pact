<?php

namespace App\Http\Controllers\API\Payload;

use App\Data\PayloadData;
use App\Enums\PayloadProcessingStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Payload;
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

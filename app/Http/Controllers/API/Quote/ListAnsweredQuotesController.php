<?php

namespace App\Http\Controllers\API\Quote;

use App\Data\PayloadData;
use App\Enums\PayloadProcessingStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\IntegrationType;
use App\Models\Payload;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

class ListAnsweredQuotesController extends Controller
{
    public function __invoke(): DataCollection|CursorPaginatedDataCollection|PaginatedDataCollection
    {
        return PayloadData::collection(
            Payload::query()
                ->join(IntegrationType::TABLE_NAME, IntegrationType::ID, '=', Payload::INTEGRATION_TYPE_ID)
                ->where(IntegrationType::CODE, '=', 'lista-cotacoes-respondidas')
                ->where(Payload::PROCESSING_STATUS, '!=', PayloadProcessingStatusEnum::COLLECTED())
                ->orWhereNull(Payload::PROCESSING_STATUS)
                ->get()
        );
    }
}

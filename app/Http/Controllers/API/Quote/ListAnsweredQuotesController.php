<?php

namespace App\Http\Controllers\API\Quote;

use App\Data\PayloadData;
use App\Enums\PayloadProcessingStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\IntegrationType;
use App\Models\Payload;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

class ListAnsweredQuotesController extends Controller
{
    public function __invoke(): DataCollection|CursorPaginatedDataCollection|PaginatedDataCollection
    {
        return PayloadData::collection(
            Payload::query()
                ->select(Payload::TABLE_NAME.'.*')
                ->join(
                    IntegrationType::TABLE_NAME,
                    IntegrationType::TABLE_NAME.'.'.IntegrationType::ID,
                    '=',
                    Payload::TABLE_NAME.'.'.Payload::INTEGRATION_TYPE_ID
                )
                ->where(
                    IntegrationType::TABLE_NAME.'.'.IntegrationType::CODE,
                    '=',
                    IntegrationType::INTEGRATION_ANSWERED_QUOTES
                )
                ->where(function (Builder $query): void {
                    $query
                        ->where(
                            Payload::TABLE_NAME.'.'.Payload::PROCESSING_STATUS,
                            '!=',
                            PayloadProcessingStatusEnum::COLLECTED()
                        )
                        ->orWhereNull(Payload::TABLE_NAME.'.'.Payload::PROCESSING_STATUS);
                })
                ->get()
        );
    }
}

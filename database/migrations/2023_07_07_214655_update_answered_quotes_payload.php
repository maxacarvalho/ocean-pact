<?php

use App\Data\PayloadData;
use App\Enums\PayloadProcessingStatusEnum;
use App\Models\IntegrationType;
use App\Models\Payload;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $items = PayloadData::collection(
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
                            PayloadProcessingStatusEnum::COLLECTED
                        )
                        ->orWhereNull(Payload::TABLE_NAME.'.'.Payload::PROCESSING_STATUS);
                })
                ->get()
        );

        foreach ($items as $item) {
            if (! $item->payload['DATA_LIMITE_RESPOSTA']) {
                $payload = $item->payload;
                try {
                    /** @var Quote $quote */
                    $quote = Quote::query()->where(Quote::QUOTE_NUMBER, '=', $payload['COTACAO'])->firstOrFail();
                    $payload['DATA_LIMITE_RESPOSTA'] = $quote->updated_at->format('Y-m-d');

                    Payload::query()->where(Payload::ID, '=', $item->id)->update([
                        Payload::PAYLOAD => $payload,
                    ]);
                } catch (Throwable $e) {
                    continue;
                }
            }
        }
    }
};

<?php

use App\Models\IntegraHub\Payload;
use App\Models\QuotesPortal\Quote;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $items = Payload::query()
            ->select('payloads.*')
            ->join('integration_types', 'integration_types.id', '=', 'payloads.integration_type_id')
            ->where('integration_types.code', '=', 'cotacoes-respondidas')
            ->where(function (Builder $query): void {
                $query
                    ->where('payloads.processing_status', '!=', 'COLLECTED')
                    ->orWhereNull('payloads.processing_status');
            })
            ->get();

        foreach ($items as $item) {
            if (! $item->payload['DATA_LIMITE_RESPOSTA']) {
                $payload = $item->payload;
                try {
                    /** @var Quote $quote */
                    $quote = Quote::query()->where('quote_number', '=', $payload['COTACAO'])->firstOrFail();
                    $payload['DATA_LIMITE_RESPOSTA'] = $quote->updated_at->format('Y-m-d');

                    Payload::query()->where('id', '=', $item->id)->update(['payload' => $payload]);
                } catch (Throwable $e) {
                    continue;
                }
            }
        }
    }
};

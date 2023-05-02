<?php

namespace App\Jobs\PayloadProcessors;

use App\Data\IncomingPaymentCondition\ProtheusPaymentConditionPayloadData;
use App\Models\PaymentCondition;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class IncomingPaymentConditionPayloadProcessor extends PayloadProcessor
{
    public function handle(): void
    {
        if (! $this->getPayload()->isReady() || $this->getPayload()->isProcessing()) {
            $this->delete();

            return;
        }

        $this->getPayload()->markAsProcessing();

        $dataCollection = ProtheusPaymentConditionPayloadData::collection($this->getPayload()->payload);

        /** @var ProtheusPaymentConditionPayloadData $item */
        foreach ($dataCollection as $item) {
            try {
                PaymentCondition::query()->updateOrCreate([
                    PaymentCondition::COMPANY_CODE => $item->EMPRESA,
                    PaymentCondition::COMPANY_CODE_BRANCH => $item->FILIAL,
                    PaymentCondition::CODE => $item->CONDICAO_PAGAMENTO,
                ], [
                    PaymentCondition::DESCRIPTION => $item->DESCRICAO,
                ]);
            } catch (ModelNotFoundException $e) {
                Log::error('IncomingPaymentConditionPayloadProcessor: could not find company', [
                    'company_code' => $item->EMPRESA,
                    'company_code_branch' => $item->FILIAL,
                    'exception' => $e->getMessage(),
                ]);

                continue;
            }
        }

        $this->getPayload()->markAsDone();
    }
}

<?php

namespace App\Enums\QuotesPortal;

use App\Enums\EnumHelperTrait;
use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum PredictedPurchaseRequestStatusEnum: string implements HasLabel
{
    use EnumHelperTrait;

    case DRAFT = 'DRAFT';
    case ACCEPTED = 'ACCEPTED';

    public function getLabel(): string
    {
        return match ($this->value) {
            'DRAFT' => Str::formatTitle(__('predicted_purchase_request.status_draft')),
            'ACCEPTED' => Str::formatTitle(__('predicted_purchase_request.status_accepted')),
        };
    }
}

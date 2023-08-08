<?php

namespace App\Enums;

use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum QuoteItemStatusEnum: string implements HasLabel
{
    use EnumHelperTrait;

    case PENDING = 'PENDING';
    case RESPONDED = 'RESPONDED';
    case ACCEPTED = 'ACCEPTED';
    case REJECTED = 'REJECTED';

    public function getLabel(): string
    {
        return match ($this->value) {
            'PENDING' => Str::formatTitle(__('quote_item.pending')),
            'RESPONDED' => Str::formatTitle(__('quote_item.responded')),
            'ACCEPTED' => Str::formatTitle(__('quote_item.accepted')),
            'REJECTED' => Str::formatTitle(__('quote_item.rejected')),
        };
    }
}

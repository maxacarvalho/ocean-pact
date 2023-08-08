<?php

namespace App\Enums;

use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum QuoteStatusEnum: string implements HasLabel
{
    use EnumHelperTrait;

    case DRAFT = 'DRAFT';
    case PENDING = 'PENDING';
    case RESPONDED = 'RESPONDED';
    case ANALYZED = 'ANALYZED';

    public function getLabel(): string
    {
        return match ($this->value) {
            'DRAFT' => Str::formatTitle(__('quote.draft')),
            'PENDING' => Str::formatTitle(__('quote.pending')),
            'RESPONDED' => Str::formatTitle(__('quote.responded')),
            'ANALYZED' => Str::formatTitle(__('quote.analyzed')),
        };
    }
}

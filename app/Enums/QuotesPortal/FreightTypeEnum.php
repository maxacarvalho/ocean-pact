<?php

namespace App\Enums\QuotesPortal;

use App\Enums\EnumHelperTrait;
use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum FreightTypeEnum: string implements HasLabel
{
    use EnumHelperTrait;

    case CIF = 'CIF';
    case FOB = 'FOB';

    public function getLabel(): string
    {
        return match ($this->value) {
            'CIF' => Str::formatTitle(__('quote.CIF')),
            'FOB' => Str::formatTitle(__('quote.FOB')),
        };
    }
}

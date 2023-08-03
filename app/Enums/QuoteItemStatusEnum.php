<?php

namespace App\Enums;

use App\Utils\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self PENDING()
 * @method static self RESPONDED()
 * @method static self ACCEPTED()
 * @method static self REJECTED()
 */
class QuoteItemStatusEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'PENDING' => Str::formatTitle(__('quote_item.pending')),
            'RESPONDED' => Str::formatTitle(__('quote_item.responded')),
            'ACCEPTED' => Str::formatTitle(__('quote_item.accepted')),
            'REJECTED' => Str::formatTitle(__('quote_item.rejected')),
        ];
    }
}

<?php

namespace App\Enums;

use App\Utils\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self DRAFT()
 * @method static self SENT()
 * @method static self RESPONDED()
 * @method static self ACCEPTED()
 * @method static self REJECTED()
 */
class QuoteStatusEnum extends Enum
{
    protected static function labels(): array
    {
        return [
            'DRAFT' => Str::formatTitle(__('quote.draft')),
            'SENT' => Str::formatTitle(__('quote.sent')),
            'RESPONDED' => Str::formatTitle(__('quote.responded')),
            'ACCEPTED' => Str::formatTitle(__('quote.accepted')),
            'REJECTED' => Str::formatTitle(__('quote.rejected')),
        ];
    }
}

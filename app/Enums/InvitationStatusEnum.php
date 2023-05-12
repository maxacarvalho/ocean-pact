<?php

namespace App\Enums;

use App\Utils\Str;

/**
 * @method static self PENDING()
 * @method static self SENT()
 * @method static self ACCEPTED()
 */
class InvitationStatusEnum extends \Spatie\Enum\Laravel\Enum
{
    protected static function labels(): array
    {
        return [
            'PENDING' => Str::formatTitle(__('invitation.pending')),
            'SENT' => Str::formatTitle(__('invitation.sent')),
            'ACCEPTED' => Str::formatTitle(__('invitation.accepted')),
        ];
    }
}

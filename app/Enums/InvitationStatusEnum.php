<?php

namespace App\Enums;

use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum InvitationStatusEnum: string implements HasLabel
{
    use EnumHelperTrait;

    case PENDING = 'PENDING';
    case SENT = 'SENT';
    case ACCEPTED = 'ACCEPTED';

    public function getLabel(): string
    {
        return match ($this->value) {
            'PENDING' => Str::formatTitle(__('invitation.pending')),
            'SENT' => Str::formatTitle(__('invitation.sent')),
            'ACCEPTED' => Str::formatTitle(__('invitation.accepted')),
        };
    }
}

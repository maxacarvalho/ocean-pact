<?php

namespace App\Enums\QuotesPortal;

use App\Utils\Str;
use Filament\Support\Contracts\HasLabel;

enum PurchaseRequestStatus: string implements HasLabel
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this->value) {
            'pending' => Str::formatTitle(__('purchase_request.pending')),
            'approved' => Str::formatTitle(__('purchase_request.approved')),
            'rejected' => Str::formatTitle(__('purchase_request.rejected')),
            'cancelled' => Str::formatTitle(__('purchase_request.cancelled')),
        };
    }
}

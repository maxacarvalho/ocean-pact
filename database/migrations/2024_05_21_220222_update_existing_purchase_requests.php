<?php

use App\Enums\QuotesPortal\PurchaseRequestStatus;
use App\Models\QuotesPortal\PurchaseRequest;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        PurchaseRequest::query()
            ->whereNull(PurchaseRequest::STATUS)
            ->update([
                PurchaseRequest::STATUS => PurchaseRequestStatus::APPROVED,
            ]);
    }
};

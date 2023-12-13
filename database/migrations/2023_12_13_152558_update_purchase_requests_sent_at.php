<?php

use App\Models\QuotesPortal\PurchaseRequest;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        PurchaseRequest::query()
            ->whereNull(PurchaseRequest::SENT_AT)
            ->update([
                PurchaseRequest::SENT_AT => now(),
            ]);
    }

    public function down(): void
    {
        //
    }
};

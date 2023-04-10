<?php

use App\Models\QuoteItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(QuoteItem::TABLE_NAME, function (Blueprint $table) {
            $table->date(QuoteItem::DELIVERY_DATE)->nullable()->after(QuoteItem::UNIT_PRICE);
        });
    }

    public function down(): void
    {
        Schema::table(QuoteItem::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};

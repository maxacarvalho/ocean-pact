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
            $table->bigInteger(QuoteItem::IPI)->default(0)->after(QuoteItem::UNIT_PRICE);
            $table->bigInteger(QuoteItem::ICMS)->default(0)->after(QuoteItem::IPI);
            $table->bigInteger(QuoteItem::FREIGHT_COST)->default(0)->after(QuoteItem::ICMS);
            $table->string(QuoteItem::FREIGHT_TYPE)->nullable()->after(QuoteItem::FREIGHT_COST);
            $table->bigInteger(QuoteItem::EXPENSES)->default(0)->after(QuoteItem::FREIGHT_TYPE);
        });
    }
};

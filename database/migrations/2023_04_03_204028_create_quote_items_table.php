<?php

use App\Models\QuoteItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(QuoteItem::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->bigInteger(QuoteItem::QUOTE_ID);
            $table->bigInteger(QuoteItem::PRODUCT_ID);
            $table->string(QuoteItem::DESCRIPTION);
            $table->string(QuoteItem::MEASUREMENT_UNIT);
            $table->string(QuoteItem::ITEM);
            $table->integer(QuoteItem::QUANTITY);
            $table->integer(QuoteItem::UNIT_PRICE);
            $table->string(QuoteItem::COMMENTS)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(QuoteItem::TABLE_NAME);
    }
};

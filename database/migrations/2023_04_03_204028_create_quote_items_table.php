<?php

use App\Models\Product;
use App\Models\Quote;
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
            $table->unsignedBigInteger(QuoteItem::QUOTE_ID);
            $table->unsignedBigInteger(QuoteItem::PRODUCT_ID);
            $table->string(QuoteItem::DESCRIPTION);
            $table->string(QuoteItem::MEASUREMENT_UNIT);
            $table->string(QuoteItem::ITEM);
            $table->integer(QuoteItem::QUANTITY);
            $table->integer(QuoteItem::UNIT_PRICE);
            $table->boolean(QuoteItem::SHOULD_BE_QUOTED)->default(true);
            $table->string(QuoteItem::COMMENTS)->nullable();
            $table->timestamps();

            $table->foreign(QuoteItem::QUOTE_ID)
                ->references(Quote::ID)
                ->on(Quote::TABLE_NAME)
                ->cascadeOnDelete();

            $table->foreign(QuoteItem::PRODUCT_ID)
                ->references(Product::ID)
                ->on(Product::TABLE_NAME);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(QuoteItem::TABLE_NAME);
    }
};

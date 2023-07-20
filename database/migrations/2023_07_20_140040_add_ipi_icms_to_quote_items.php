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
            $table->after(QuoteItem::UNIT_PRICE, function (Blueprint $table) {
                $table->unsignedInteger(QuoteItem::IPI)->default(0);
                $table->unsignedInteger(QuoteItem::ICMS)->default(0);
            });
        });
    }
};

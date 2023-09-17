<?php

use App\Models\QuoteItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            $table->string('status')->default('PENDING')->after('should_be_quoted');
        });

        QuoteItem::query()->each(function (QuoteItem $quoteItem) {
            if ($quoteItem->unit_price > 0 && null !== $quoteItem->delivery_date) {
                $quoteItem->status = 'RESPONDED';
                $quoteItem->save();
            }
        });
    }
};

<?php

use App\Models\QuotesPortal\QuoteItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            $table->renameColumn('ipi', 'ipi_old');
            $table->renameColumn('icms', 'icms_old');
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->decimal('ipi', 10, 2)->after('unit_price');
            $table->decimal('icms', 10, 2)->after('unit_price');
        });

        QuoteItem::query()->each(function (QuoteItem $item) {
            if ($item->ipi_old > 0) {
                $item->ipi = $item->ipi_old / 100;
            }

            if ($item->icms_old > 0) {
                $item->icms = $item->icms_old / 100;
            }

            $item->save();
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->dropColumn('ipi_old');
            $table->dropColumn('icms_old');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            $table->after('comments', function (Blueprint $table) {
                $table->string('seller_image')->nullable();
                $table->string('buyer_image')->nullable();
            });
        });
    }

    public function down(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            $table->dropColumn('seller_image');
            $table->dropColumn('buyer_image');
        });
    }
};

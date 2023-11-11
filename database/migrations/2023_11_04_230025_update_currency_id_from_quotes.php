<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('currency_id')->nullable()->change();
        });

        DB::table('quotes')
            ->where('currency_id', '')
            ->update(['currency_id' => null]);

        Schema::table('quotes', function (Blueprint $table) {
            $table->unsignedBigInteger('currency_id')->nullable()->change();
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->string('currency_id')->nullable()->change();
        });

        DB::table('quotes')
            ->whereNull('currency_id')
            ->update(['currency_id' => '']);

        Schema::table('quotes', function (Blueprint $table) {
            $table->string('currency_id')->nullable(false)->change();
        });
    }
};

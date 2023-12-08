<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->unsignedBigInteger('replaced_by')
                ->nullable()
                ->after('currency_id');

            $table->foreign('replaced_by')
                ->references('id')
                ->on('quotes')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['replaced_by']);
            $table->dropColumn('replaced_by');
        });
    }
};

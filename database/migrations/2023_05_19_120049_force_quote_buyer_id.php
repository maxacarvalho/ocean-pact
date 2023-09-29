<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign('quotes_buyer_id_foreign');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->unsignedBigInteger('buyer_id')->nullable(false)->change();
            $table
                ->foreign('buyer_id')
                ->references('id')
                ->on('users');
        });
    }
};

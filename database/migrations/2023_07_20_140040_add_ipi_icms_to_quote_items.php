<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            $table->after('unit_price', function (Blueprint $table) {
                $table->unsignedInteger('ipi')->default(0);
                $table->unsignedInteger('icms')->default(0);
            });
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_conditions', function (Blueprint $table) {
            $table->string('description', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('payment_conditions', function (Blueprint $table) {
            $table->string('description', 15)->change();
        });
    }
};

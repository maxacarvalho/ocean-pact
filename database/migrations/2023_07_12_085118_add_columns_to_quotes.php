<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->after('comments', function (Blueprint $table) {
                $table->bigInteger('expenses')->default(0);
                $table->bigInteger('freight_cost')->default(0);
                $table->string('freight_type')->nullable();
            });
        });
    }
};

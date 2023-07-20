<?php

use App\Models\Quote;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Quote::TABLE_NAME, function (Blueprint $table) {
            $table->after(Quote::COMMENTS, function (Blueprint $table) {
                $table->bigInteger(Quote::EXPENSES)->default(0);
                $table->bigInteger(Quote::FREIGHT_COST)->default(0);
                $table->string(Quote::FREIGHT_TYPE)->nullable();
            });
        });
    }
};

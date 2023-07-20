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
            $table->string(Quote::CURRENCY_ID)->after(Quote::FREIGHT_TYPE);
        });
    }
};

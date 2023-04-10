<?php

use App\Enums\QuoteStatusEnum;
use App\Models\Quote;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Quote::TABLE_NAME, function (Blueprint $table) {
            $table->string(Quote::STATUS)->default(QuoteStatusEnum::DRAFT())->after(Quote::VALID_UNTIL);
        });
    }

    public function down(): void
    {
        Schema::table(Quote::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};

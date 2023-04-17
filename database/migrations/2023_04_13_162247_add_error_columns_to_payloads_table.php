<?php

use App\Models\Payload;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Payload::TABLE_NAME, function (Blueprint $table) {
            $table->text(Payload::ERROR)->nullable()->after(Payload::PROCESSING_STATUS);
        });
    }

    public function down(): void
    {
        Schema::table(Payload::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};

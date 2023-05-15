<?php

use App\Models\Supplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Supplier::TABLE_NAME, function (Blueprint $table) {
            $table->string(Supplier::STORE, 50)->change();
            $table->string(Supplier::CODE, 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table(Supplier::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};

<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(User::TABLE_NAME, function (Blueprint $table) {
            $table->unsignedBigInteger(User::SUPPLIER_ID)->nullable()->index()->after(User::BUYER_CODE);

            $table->foreign(User::SUPPLIER_ID)
                ->references(User::ID)
                ->on(User::TABLE_NAME)
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table(User::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};

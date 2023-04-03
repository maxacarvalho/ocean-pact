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
            $table->string(User::BUYER_CODE, 10)->nullable()->index()->after(User::EMAIL);
        });
    }

    public function down(): void
    {
        Schema::table(User::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(User::BUYER_CODE);
        });
    }
};

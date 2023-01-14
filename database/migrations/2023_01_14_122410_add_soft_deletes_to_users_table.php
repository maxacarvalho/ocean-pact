<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(User::TABLE_NAME, static function (Blueprint $table) {
            $table->softDeletes();
            $table->index(User::DELETED_AT);
        });
    }

    public function down(): void
    {
        Schema::table(User::TABLE_NAME, static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

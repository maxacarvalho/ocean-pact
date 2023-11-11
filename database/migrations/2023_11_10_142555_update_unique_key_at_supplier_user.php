<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_user', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['user_id']);

            $table->dropUnique(['supplier_id', 'user_id']);

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->cascadeOnDelete();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->unique(['supplier_id', 'user_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::table('supplier_user', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['user_id']);

            $table->dropUnique(['supplier_id', 'user_id', 'code']);

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->cascadeOnDelete();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->unique(['supplier_id', 'user_id']);
        });
    }
};

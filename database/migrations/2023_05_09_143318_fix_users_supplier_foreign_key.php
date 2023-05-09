<?php

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(User::TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign('users_supplier_id_foreign');

            $table->foreign(User::SUPPLIER_ID)
                ->references(Supplier::ID)
                ->on(Supplier::TABLE_NAME)
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table(User::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};

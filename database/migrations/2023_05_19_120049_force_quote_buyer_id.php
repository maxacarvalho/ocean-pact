<?php

use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Quote::TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign('quotes_buyer_id_foreign');
        });

        Schema::table(Quote::TABLE_NAME, function (Blueprint $table) {
            $table->unsignedBigInteger(Quote::BUYER_ID)->nullable(false)->change();
            $table
                ->foreign(Quote::BUYER_ID)
                ->references(User::ID)
                ->on(User::TABLE_NAME);
        });
    }
};

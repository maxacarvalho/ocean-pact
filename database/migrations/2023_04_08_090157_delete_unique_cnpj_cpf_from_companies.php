<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(Company::TABLE_NAME, function (Blueprint $table) {
            $table->dropUnique(['cnpj']);
        });
    }

    public function down(): void
    {
        Schema::table(Company::TABLE_NAME, function (Blueprint $table) {
            //
        });
    }
};
